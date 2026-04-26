-- ==========================================
-- SCRIPT SQL SUPABASE: EduRecruit Pro Phase 1
-- ==========================================

-- 1. Création du type ENUM pour les rôles
DO $$ BEGIN
    CREATE TYPE public.user_role AS ENUM ('teacher', 'recruiter');
EXCEPTION
    WHEN duplicate_object THEN null;
END $$;

-- 2. Création de la table "profiles" liée à auth.users
CREATE TABLE IF NOT EXISTS public.profiles (
  id UUID PRIMARY KEY REFERENCES auth.users(id) ON DELETE CASCADE,
  role public.user_role NOT NULL,
  full_name TEXT NOT NULL,
  city TEXT,
  bio TEXT,
  is_validated BOOLEAN DEFAULT false,
  created_at TIMESTAMPTZ DEFAULT timezone('utc'::text, now()) NOT NULL,
  updated_at TIMESTAMPTZ DEFAULT timezone('utc'::text, now()) NOT NULL
);

ALTER TABLE public.profiles ENABLE ROW LEVEL SECURITY;

CREATE POLICY "Les profils sont visibles par tous les utilisateurs"
  ON public.profiles FOR SELECT TO authenticated USING (true);

CREATE POLICY "Les utilisateurs peuvent mettre à jour leur propre profil"
  ON public.profiles FOR UPDATE TO authenticated USING (auth.uid() = id);

-- 3. Création de la table "teacher_details"
CREATE TABLE IF NOT EXISTS public.teacher_details (
  id UUID PRIMARY KEY REFERENCES public.profiles(id) ON DELETE CASCADE,
  experience_years INT DEFAULT 0,
  diploma TEXT,
  specialties TEXT DEFAULT '',
  availabilities JSONB DEFAULT '{}'::jsonb, 
  cv_url TEXT,
  created_at TIMESTAMPTZ DEFAULT timezone('utc'::text, now()) NOT NULL,
  updated_at TIMESTAMPTZ DEFAULT timezone('utc'::text, now()) NOT NULL
);

ALTER TABLE public.teacher_details ENABLE ROW LEVEL SECURITY;

CREATE POLICY "Les détails des enseignants sont visibles par tous"
  ON public.teacher_details FOR SELECT TO authenticated USING (true);

CREATE POLICY "Les enseignants peuvent modifier leurs propres détails"
  ON public.teacher_details FOR ALL TO authenticated USING (auth.uid() = id);

-- 4. Création de la table "availabilities"
CREATE TABLE IF NOT EXISTS public.availabilities (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  teacher_id UUID NOT NULL REFERENCES public.profiles(id) ON DELETE CASCADE,
  jour_semaine TEXT NOT NULL CHECK (jour_semaine IN ('lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche')),
  matin BOOLEAN DEFAULT false,
  apres_midi BOOLEAN DEFAULT false,
  created_at TIMESTAMPTZ DEFAULT timezone('utc'::text, now()) NOT NULL,
  CONSTRAINT unique_teacher_day UNIQUE(teacher_id, jour_semaine)
);

ALTER TABLE public.availabilities ENABLE ROW LEVEL SECURITY;

CREATE POLICY "Les disponibilités sont visibles par tous"
  ON public.availabilities FOR SELECT TO authenticated USING (true);

CREATE POLICY "Les enseignants peuvent gérer leurs propres disponibilités"
  ON public.availabilities FOR ALL TO authenticated USING (auth.uid() = teacher_id);

-- ==========================================
-- 5. Triggers & Functions
-- ==========================================
CREATE OR REPLACE FUNCTION public.handle_updated_at()
RETURNS TRIGGER AS $$
BEGIN
  NEW.updated_at = now();
  RETURN NEW;
END;
$$ LANGUAGE plpgsql;

DROP TRIGGER IF EXISTS on_profiles_updated ON public.profiles;
CREATE TRIGGER on_profiles_updated
  BEFORE UPDATE ON public.profiles
  FOR EACH ROW EXECUTE PROCEDURE public.handle_updated_at();

DROP TRIGGER IF EXISTS on_teacher_details_updated ON public.teacher_details;
CREATE TRIGGER on_teacher_details_updated
  BEFORE UPDATE ON public.teacher_details
  FOR EACH ROW EXECUTE PROCEDURE public.handle_updated_at();

-- Création magique de profile
CREATE OR REPLACE FUNCTION public.handle_new_user() 
RETURNS TRIGGER AS $$
BEGIN
  INSERT INTO public.profiles (id, full_name, role)
  VALUES (
    NEW.id,
    COALESCE(NEW.raw_user_meta_data->>'full_name', 'Utilisateur'),
    COALESCE(CAST(NEW.raw_user_meta_data->>'role' AS public.user_role), 'teacher')
  );
  IF NEW.raw_user_meta_data->>'role' = 'teacher' THEN
    INSERT INTO public.teacher_details (id) VALUES (NEW.id);
  END IF;
  RETURN NEW;
END;
$$ LANGUAGE plpgsql SECURITY DEFINER;

DROP TRIGGER IF EXISTS on_auth_user_created ON auth.users;
CREATE TRIGGER on_auth_user_created
  AFTER INSERT ON auth.users
  FOR EACH ROW EXECUTE PROCEDURE public.handle_new_user();votre droite.
  
  

-- ==========================================
-- 6. Storage & Policies pour les CVs (Privé)
-- ==========================================
INSERT INTO storage.buckets (id, name, public) 
VALUES ('cvs', 'cvs', false) 
ON CONFLICT (id) DO NOTHING;

CREATE POLICY "Les profs peuvent uploader leur propre CV"
ON storage.objects FOR INSERT TO authenticated
WITH CHECK (
  bucket_id = 'cvs' AND 
  (auth.uid())::text = (string_to_array(name, '/'))[1]
);

CREATE POLICY "Les profs peuvent voir leur CV"
ON storage.objects FOR SELECT TO authenticated
USING (
  bucket_id = 'cvs' AND 
  (auth.uid())::text = (string_to_array(name, '/'))[1]
);

CREATE POLICY "Les recruteurs voient tous les CVs"
ON storage.objects FOR SELECT TO authenticated
USING (
  bucket_id = 'cvs' AND 
  EXISTS (
    SELECT 1 FROM public.profiles 
    WHERE id = auth.uid() AND role = 'recruiter'
  )
);

-- ==========================================
-- 7. Table Missions (Phase 2)
-- ==========================================
CREATE TABLE IF NOT EXISTS public.missions (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  recruiter_id UUID NOT NULL REFERENCES public.profiles(id) ON DELETE CASCADE,
  title TEXT NOT NULL,
  description TEXT NOT NULL,
  location TEXT,
  level TEXT, 
  created_at TIMESTAMPTZ DEFAULT timezone('utc'::text, now()) NOT NULL,
  is_active BOOLEAN DEFAULT true
);

ALTER TABLE public.missions ENABLE ROW LEVEL SECURITY;

CREATE POLICY "Les missions sont visibles par tous"
  ON public.missions FOR SELECT TO authenticated USING (true);

CREATE POLICY "Les recruteurs peuvent publier des missions"
  ON public.missions FOR INSERT TO authenticated WITH CHECK (auth.uid() = recruiter_id);

CREATE POLICY "Les recruteurs peuvent modifier leurs propres missions"
  ON public.missions FOR UPDATE TO authenticated USING (auth.uid() = recruiter_id);

-- ==========================================
-- 8. Fonctions RPC (Administration MVP)
-- ==========================================
-- Permet de valider un compte en outrepassant les RLS (SECURITY DEFINER)
CREATE OR REPLACE FUNCTION public.validate_teacher(target_id UUID)
RETURNS void AS $$
BEGIN
  UPDATE public.profiles SET is_validated = true WHERE id = target_id;
END;
$$ LANGUAGE plpgsql SECURITY DEFINER;

-- Permet de récupérer les profils en attente sans contrainte de RLS (Session anonyme Ok pour l'MVP Admin)
CREATE OR REPLACE FUNCTION public.get_pending_profiles()
RETURNS TABLE (
  id UUID,
  full_name TEXT,
  city TEXT,
  created_at TIMESTAMPTZ,
  diploma TEXT,
  cv_url TEXT
) AS $$
BEGIN
  RETURN QUERY
  SELECT 
    p.id, p.full_name, p.city, p.created_at,
    td.diploma, td.cv_url
  FROM public.profiles p
  LEFT JOIN public.teacher_details td ON td.id = p.id
  WHERE p.role = 'teacher' AND p.is_validated = false
  ORDER BY p.created_at ASC;
END;
$$ LANGUAGE plpgsql SECURITY DEFINER;

-- ==========================================
-- 9. Table Applications (Candidatures - Phase 3)
-- ==========================================
DO $$ BEGIN
    CREATE TYPE public.application_status AS ENUM ('pending', 'accepted', 'rejected');
EXCEPTION
    WHEN duplicate_object THEN null;
END $$;

CREATE TABLE IF NOT EXISTS public.applications (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  mission_id UUID NOT NULL REFERENCES public.missions(id) ON DELETE CASCADE,
  teacher_id UUID NOT NULL REFERENCES public.profiles(id) ON DELETE CASCADE,
  status public.application_status DEFAULT 'pending',
  message TEXT,
  created_at TIMESTAMPTZ DEFAULT timezone('utc'::text, now()) NOT NULL,
  CONSTRAINT unique_candidature UNIQUE(mission_id, teacher_id)
);

ALTER TABLE public.applications ENABLE ROW LEVEL SECURITY;

CREATE POLICY "Les recruteurs voient les candidatures de leurs missions"
  ON public.applications FOR SELECT TO authenticated
  USING (EXISTS (
    SELECT 1 FROM public.missions WHERE id = applications.mission_id AND recruiter_id = auth.uid()
  ));

CREATE POLICY "Les recruteurs peuvent modifier le statut"
  ON public.applications FOR UPDATE TO authenticated
  USING (EXISTS (
    SELECT 1 FROM public.missions WHERE id = applications.mission_id AND recruiter_id = auth.uid()
  ));

CREATE POLICY "Les professeurs voient leurs propres candidatures"
  ON public.applications FOR SELECT TO authenticated
  USING (teacher_id = auth.uid());

CREATE POLICY "Les professeurs peuvent postuler librement"
  ON public.applications FOR INSERT TO authenticated
  WITH CHECK (teacher_id = auth.uid());

-- ==========================================
-- 10. MODE DEMO JURY (Mode "Portes Ouvertes")
-- EXÉCUTEZ CE BLOC POUR DÉSACTIVER LES BLOCAGES RLS
-- ==========================================

-- 1. On désactive la sécurité sur toutes les tables
ALTER TABLE public.profiles DISABLE ROW LEVEL SECURITY;
ALTER TABLE public.teacher_details DISABLE ROW LEVEL SECURITY;
ALTER TABLE public.availabilities DISABLE ROW LEVEL SECURITY;
ALTER TABLE public.missions DISABLE ROW LEVEL SECURITY;
ALTER TABLE public.applications DISABLE ROW LEVEL SECURITY;

-- 2. On supprime le trigger d'inscription qui posait problème (L'insertion se fait maintenant via auth.js)
DROP TRIGGER IF EXISTS on_auth_user_created ON auth.users;
DROP FUNCTION IF EXISTS public.handle_new_user();

-- 3. On autorise tout le monde à lire et écrire les PDF dans le bucket 'cvs'
DROP POLICY IF EXISTS "Les profs peuvent uploader leur propre CV" ON storage.objects;
DROP POLICY IF EXISTS "Les profs peuvent voir leur CV" ON storage.objects;
DROP POLICY IF EXISTS "Les recruteurs voient tous les CVs" ON storage.objects;

CREATE POLICY "Open Insert Bucket" ON storage.objects FOR INSERT TO public WITH CHECK (bucket_id = 'cvs');
CREATE POLICY "Open Select Bucket" ON storage.objects FOR SELECT TO public USING (bucket_id = 'cvs');
CREATE POLICY "Open Update Bucket" ON storage.objects FOR UPDATE TO public USING (bucket_id = 'cvs');

-- ==========================================
-- 11. Fonctionnalités Avancées (Ratings, Messages, Alertes)
-- ==========================================
ALTER TABLE public.profiles ADD COLUMN IF NOT EXISTS email_alerts BOOLEAN DEFAULT true;

CREATE TABLE IF NOT EXISTS public.ratings (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  mission_id UUID NOT NULL REFERENCES public.missions(id) ON DELETE CASCADE,
  rater_id UUID NOT NULL REFERENCES public.profiles(id) ON DELETE CASCADE,
  target_id UUID NOT NULL REFERENCES public.profiles(id) ON DELETE CASCADE,
  score INT NOT NULL CHECK (score >= 1 AND score <= 5),
  comment TEXT,
  created_at TIMESTAMPTZ DEFAULT timezone('utc'::text, now()) NOT NULL
);
ALTER TABLE public.ratings DISABLE ROW LEVEL SECURITY;

CREATE TABLE IF NOT EXISTS public.chat_messages (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  application_id UUID NOT NULL REFERENCES public.applications(id) ON DELETE CASCADE,
  sender_id UUID NOT NULL REFERENCES public.profiles(id) ON DELETE CASCADE,
  content TEXT NOT NULL,
  created_at TIMESTAMPTZ DEFAULT timezone('utc'::text, now()) NOT NULL
);
ALTER TABLE public.chat_messages DISABLE ROW LEVEL SECURITY;

-- 12. Ajout colonne availability sur missions
ALTER TABLE public.missions ADD COLUMN IF NOT EXISTS availability TEXT;

