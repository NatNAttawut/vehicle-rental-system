(async function () {
  if (!window.sb) return;
  const parts = (window.location.pathname || '').split('/').filter(Boolean);
  const base = (parts.length && !parts[0].includes('.')) ? ('/' + parts[0]) : '';
  const { data: { session } } = await window.sb.auth.getSession();
  if (!session) {
    window.location.href = `${base}/Signin/signin.php`;
    return;
  }
  const { data, error } = await window.sb
    .from('customer')
    .select('cust_role')
    .eq('auth_id', session.user.id)
    .single();

  if (error || !data || data.cust_role !== 'admin') {
    window.location.href = `${base}/User/Uindex.php`;
  }
})();
