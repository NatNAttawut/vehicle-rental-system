// Global Supabase client for this project
// anon key is ok to expose in browser when RLS policies are configured.
(function () {
  if (!window.supabase) {
    console.error('supabase-js is not loaded. Include: https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2');
    return;
  }

  const SUPABASE_URL = 'https://ucpfkzoswswaxsiovxon.supabase.co';
  const SUPABASE_ANON_KEY = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InVjcGZrem9zd3N3YXhzaW92eG9uIiwicm9sZSI6ImFub24iLCJpYXQiOjE3Njg3MDU1NjQsImV4cCI6MjA4NDI4MTU2NH0.z0C8t5V1CNfSQ1IaJwKRLFiAZR-K4m-uIFQQKA0P_Zg';

  window.sb = window.supabase.createClient(SUPABASE_URL, SUPABASE_ANON_KEY);
})();
