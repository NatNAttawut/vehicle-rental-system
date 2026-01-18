const sb = window.sb;

async function loadBookings() {
  const { data, error } = await sb
    .from('booking')
    .select('*')
    .order('book_id', { ascending: false });

  if (error) { alert(error.message); return; }

  const tbody = document.getElementById('bookingRows');
  tbody.innerHTML = '';

  for (const b of data) {
    tbody.innerHTML += `
      <tr>
        <td>${b.book_id}</td>
        <td>${b.car_id}</td>
        <td>${b.cust_id}</td>
        <td>${b.book_start ?? ''}</td>
        <td>${b.book_exp ?? ''}</td>
        <td>${b.book_status ?? ''}</td>
        <td>
          <button onclick="setStatus(${b.book_id}, 'approved')">ยืนยันจอง</button>
          <button onclick="setStatus(${b.book_id}, 'picked_up')">ยืนยันรับรถ</button>
          <button onclick="setStatus(${b.book_id}, 'returned')">ยืนยันคืนรถ</button>
        </td>
      </tr>
    `;
  }
}

async function setStatus(bookId, status) {
  const { error } = await sb.from('booking')
    .update({ book_status: status })
    .eq('book_id', bookId);

  if (error) { alert(error.message); return; }
  await loadBookings();
}

window.addEventListener('DOMContentLoaded', loadBookings);
