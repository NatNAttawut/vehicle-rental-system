// Updates the header login area based on current session
(async function () {
  if (!window.sb) return;

  // Try to make links work whether the project is hosted at domain root or inside a subfolder
  function getBasePath() {
    const parts = (window.location.pathname || '').split('/').filter(Boolean);
    if (!parts.length) return '';
    // If the first segment looks like a file (e.g., index.php), we're at domain root
    if (parts[0].includes('.')) return '';
    return '/' + parts[0];
  }

  const BASE = getBasePath();

  async function loadProfile(userId) {
    try {
      // Keep this minimal to avoid column-not-found errors
      const { data, error } = await window.sb
        .from('customer')
        .select('cust_uname,cust_role')
        .eq('auth_id', userId)
        .single();
      if (error) throw error;
      return data;
    } catch (e) {
      console.warn('Cannot load profile from customer:', e?.message || e);
      return null;
    }
  }

  async function handleLogout() {
    await sb.auth.signOut();
    window.location.href = '../index.php';
  }

  // expose for onclick usage if needed
  window.handleLogout = handleLogout;

  const authDiv = document.getElementById('auth-container');
  if (!authDiv) return;

  const { data: { session } } = await window.sb.auth.getSession();

  if (!session) {
    // logged out
    authDiv.innerHTML = `<a href="${BASE}/Signup/signup.php">Sign up</a> <a href="${BASE}/Signin/signin.php">Sign in</a>`;
    return;
  }

  const profile = await loadProfile(session.user.id);
  const displayName = (profile && (profile.cust_uname || session.user.email)) || (session.user.email || 'User');

  authDiv.innerHTML = `
    <span style="font-weight:bold; margin-right: 15px; color: black;">${displayName}</span>
    <button onclick="handleLogout()" style="background:none; border:1px solid black; padding:5px 10px; cursor:pointer; border-radius:4px;">Log out</button>
  `;
})();
