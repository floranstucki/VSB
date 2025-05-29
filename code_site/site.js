<script>
  // Fonction d'affichage/masquage
  function toggleSection(id, trigger) {
    var section = document.getElementById(id);
    var icon = trigger.querySelector(".toggle-icon");

    if (section.style.display === "none" || section.style.display === "") {
      section.style.display = "block";
      if (icon) icon.textContent = "➖";
    } else {
      section.style.display = "none";
      if (icon) icon.textContent = "➕";
    }
  }

  // Réinitialisation des icônes au chargement
  document.addEventListener("DOMContentLoaded", function () {
    // Masquer toutes les sections au chargement
    const sections = ["creation", "gestion", "supervision"];
    sections.forEach(function (id) {
      const section = document.getElementById(id);
      if (section) section.style.display = "none";
    });

    // Remettre toutes les icônes à +
    const icons = document.querySelectorAll(".toggle-icon");
    icons.forEach(function (icon) {
      icon.textContent = "➕";
    });
  });
</script>
