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


const modal = document.getElementById("movieModal");

document.querySelectorAll(".movie-card").forEach(card => {

    card.addEventListener("click", () => {

        document.getElementById("modalPoster").src =
            card.dataset.poster;

        document.getElementById("modalTitle").textContent =
            card.dataset.title;

        document.getElementById("modalDescription").textContent =
            card.dataset.description;

        document.getElementById("modalMeta").innerHTML = `
            <p>${card.dataset.age}</p>
            <p>${card.dataset.genre}</p>
            <p>${card.dataset.duration}</p>
            <p>${card.dataset.year}</p>
        `;

        document.getElementById("modalShowtimes").style.display = "none";

        modal.classList.add("show");
    });

});

document.getElementById("closeModal").addEventListener("click", () => {
    modal.classList.remove("show");
});

document.getElementById("showTicketsBtn").addEventListener("click", () => {

    const times = document.getElementById("modalShowtimes");

    if(times.style.display === "none"){
        times.style.display = "block";
    } else {
        times.style.display = "none";
    }

});


const searchInput = document.getElementById("movieSearch");

if(searchInput){

    searchInput.addEventListener("keyup", () => {

        const filter =
            searchInput.value.toLowerCase();

        document.querySelectorAll(".movieItem")
        .forEach(item => {

            const title =
                item.textContent.toLowerCase();

            item.style.display =
                title.includes(filter)
                ? ""
                : "none";

        });

    });

}

function confirmDelete(title)
{
    return confirm(
        `Ben je zeker dat je "${title}" wilt verwijderen?`
    );
}