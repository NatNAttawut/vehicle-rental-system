(async function () {
  if (!window.sb) return;
  const { data: { session } } = await window.sb.auth.getSession();
  if (!session) {
    const parts = (window.location.pathname || '').split('/').filter(Boolean);
    const base = (parts.length && !parts[0].includes('.')) ? ('/' + parts[0]) : '';
    window.location.href = `${base}/Signin/signin.php`;
  }
})();
