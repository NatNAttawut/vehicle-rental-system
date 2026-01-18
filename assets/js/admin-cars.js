const sb = window.sb;

async function loadCars() {
  const { data, error } = await sb.from('car').select('*').order('car_id', { ascending: false });
  if (error) { alert(error.message); return; }

  const tbody = document.getElementById('carRows');
  tbody.innerHTML = '';
  for (const c of data) {
    tbody.innerHTML += `
      <tr>
        <td>${c.car_name ?? ''}</td>
        <td>${c.car_num ?? ''}</td>
        <td>${c.car_regis ?? ''}</td>
        <td>${c.car_brand ?? ''}</td>
        <td>${c.car_model ?? ''}</td>
        <td>${c.car_status ?? ''}</td>
        <td>${c.car_img ? `<img src="${c.car_img}" style="height:40px;">` : ''}</td>
        <td>
          <button onclick='openEdit(${JSON.stringify(c)})'>แก้ไข</button>
          <button onclick="delCar(${c.car_id})">ลบ</button>
        </td>
      </tr>
    `;
  }
}

function openAdd() {
  document.getElementById('modalTitle').innerText = 'เพิ่มข้อมูลรถ';
  document.getElementById('car_id').value = '';
  ['car_name','car_num','car_regis','car_brand','car_model','car_img'].forEach(id => document.getElementById(id).value = '');
  document.getElementById('car_status').value = 'available';
  document.getElementById('carModal').style.display = 'block';
}

function openEdit(c) {
  document.getElementById('modalTitle').innerText = 'แก้ไขข้อมูลรถ';
  document.getElementById('car_id').value = c.car_id;
  document.getElementById('car_name').value = c.car_name ?? '';
  document.getElementById('car_num').value = c.car_num ?? '';
  document.getElementById('car_regis').value = c.car_regis ?? '';
  document.getElementById('car_brand').value = c.car_brand ?? '';
  document.getElementById('car_model').value = c.car_model ?? '';
  document.getElementById('car_status').value = c.car_status ?? 'available';
  document.getElementById('car_img').value = c.car_img ?? '';
  document.getElementById('carModal').style.display = 'block';
}

function closeModal() {
  document.getElementById('carModal').style.display = 'none';
}

async function saveCar() {
  const car_id = document.getElementById('car_id').value || null;

  const payload = {
    car_name: document.getElementById('car_name').value.trim(),
    car_num: document.getElementById('car_num').value.trim(),
    car_regis: document.getElementById('car_regis').value.trim(),
    car_brand: document.getElementById('car_brand').value.trim(),
    car_model: document.getElementById('car_model').value.trim(),
    car_status: document.getElementById('car_status').value,
    car_img: document.getElementById('car_img').value.trim()
  };

  let res;
  if (car_id) {
    res = await sb.from('car').update(payload).eq('car_id', car_id);
  } else {
    res = await sb.from('car').insert([payload]);
  }

  if (res.error) { alert(res.error.message); return; }
  closeModal();
  await loadCars();
  alert('บันทึกสำเร็จ');
}

async function delCar(id) {
  if (!confirm('หากต้องการลบข้อมูลโปรด “ยืนยัน” การลบ')) return;
  const { error } = await sb.from('car').delete().eq('car_id', id);
  if (error) { alert(error.message); return; }
  await loadCars();
}

window.addEventListener('DOMContentLoaded', loadCars);
