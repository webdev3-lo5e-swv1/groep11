  const bar = document.getElementById('locationsBar');
  const arrow = document.getElementById('locationsArrow');
  const dropdown = document.getElementById('locationsDropdown');
  const label = document.getElementById('locationsLabel');

  bar.addEventListener('click', () => {
    const isOpen = dropdown.classList.toggle('open');
    arrow.classList.toggle('open', isOpen);
  });

  dropdown.querySelectorAll('.dropdown-option').forEach(opt => {
    opt.addEventListener('click', (e) => {
      e.stopPropagation();
      dropdown.querySelectorAll('.dropdown-option').forEach(o => o.classList.remove('active'));
      opt.classList.add('active');
      label.textContent = opt.textContent;
      dropdown.classList.remove('open');
      arrow.classList.remove('open');
    });
  });

  document.addEventListener('click', (e) => {
    if (!bar.contains(e.target)) {
      dropdown.classList.remove('open');
      arrow.classList.remove('open');
    }
  });