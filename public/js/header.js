function side_bar_tuerquita_in() {
    let side_bar = document.getElementById('side_bar');
    let tuerquita = document.getElementById('tuerquita');

    side_bar.classList.add('side_bar_hovered');
    tuerquita.style.transform = 'rotate(0deg)';
    tuerquita.removeAttribute('onclick');
    tuerquita.setAttribute('onclick', 'side_bar_tuerquita_out()');
}

function side_bar_tuerquita_out() {
    let side_bar = document.getElementById('side_bar');
    let tuerquita = document.getElementById('tuerquita');

    side_bar.classList.remove('side_bar_hovered');
    tuerquita.style.transform = 'rotate(359deg)';
    tuerquita.removeAttribute('onclick');
    tuerquita.setAttribute('onclick', 'side_bar_tuerquita_in()');
}

document.querySelector('#customSearch').addEventListener('input', function () {
    table.search(this.value).draw();
})