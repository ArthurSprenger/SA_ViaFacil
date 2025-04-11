const spinner = document.querySelector('.spinner');

if (spinner) {
  let angle = 0;

  function rotateSpinner() {
    angle += 5;
    spinner.style.transform = `rotate(${angle}deg)`;
  }

  setInterval(rotateSpinner, 50);
}

setTimeout(() => {
  window.location.href = 'login.html';
}, 3000);

document.addEventListener("DOMContentLoaded", function() {
  console.log("Dashboard carregado");
});
