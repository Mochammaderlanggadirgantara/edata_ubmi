function showWeek(weekNum) {
    document.querySelectorAll('.mingguan').forEach(div => div.style.display = 'none');
    document.getElementById('minggu' + weekNum).style.display = 'block';

    document.getElementById('label-minggu').textContent = weekNum === 0 ? '(Waktu Saldo)' : `(Minggu ke-${weekNum})`;

    for (let i = 0; i <= 4; i++) {
        const btn = document.getElementById('btn-minggu' + i);
        btn.classList.remove('active', 'btn-dark');
        btn.classList.add('btn-outline-dark');
    }

    const activeBtn = document.getElementById('btn-minggu' + weekNum);
    activeBtn.classList.remove('btn-outline-dark');
    activeBtn.classList.add('btn-dark', 'active');
}
