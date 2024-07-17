function printCertificate() {
  const inputs = document.querySelectorAll('input');
  inputs.forEach(input => {
      const span = document.createElement('span');
      span.textContent = input.value;
      span.style.borderBottom = '1px solid black';
      span.style.paddingRight = '5px';
      input.parentNode.replaceChild(span, input);
  });

  window.print();

  // Optionally reload the page after print to reset the form
  window.location.reload();
}