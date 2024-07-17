<!-- gdpr_modal.php -->
<div id="gdprModal" class="modal" style="display: none">
  <div class="modal-content">
    <span class="close">&times;</span>
    <h2>Politica de Confidențialitate</h2>
    <p>Introducere</p>
    <p>Aplicația MedFM respectă confidențialitatea și protecția datelor personale ale utilizatorilor săi. Această politică explică modul în care colectăm, utilizăm, stocăm și protejăm informațiile personale în conformitate cu Regulamentul General privind Protecția Datelor (GDPR).</p>
    <h3>1. Informații Colectate</h3>
    <p>Colectăm următoarele categorii de date personale:</p>
    <ul>
      <li>Date de identificare: Nume, prenume, CNP.</li>
      <li>Date de contact: Adresă de e-mail, număr de telefon.</li>
      <li>Date medicale: Istoric medical, programări, medicamente prescrise.</li>
      <li>Date tehnice: Adresa IP, cookie-uri, date de autentificare.</li>
    </ul>
    <h3>2. Scopurile Colectării Datelor</h3>
    <p>Datele colectate sunt utilizate în următoarele scopuri:</p>
    <ul>
      <li>Pentru a oferi servicii medicale personalizate.</li>
      <li>Pentru a gestiona și programa consultațiile medicale.</li>
      <li>Pentru a comunica cu utilizatorii în legătură cu serviciile oferite.</li>
      <li>Pentru a îmbunătăți funcționalitatea și securitatea aplicației.</li>
    </ul>
    <h3>3. Temeiul Legal pentru Prelucrarea Datelor</h3>
    <p>Prelucrarea datelor personale se bazează pe următoarele temeiuri legale:</p>
    <ul>
      <li>Consimțământul explicit al utilizatorului.</li>
      <li>Executarea unui contract încheiat între utilizator și MedFM.</li>
      <li>Îndeplinirea obligațiilor legale.</li>
      <li>Interesele legitime ale MedFM de a asigura funcționarea corespunzătoare a aplicației.</li>
    </ul>
    <h3>4. Divulgarea Datelor Personale</h3>
    <p>Nu vom divulga datele personale ale utilizatorilor către terți fără consimțământul acestora, cu excepția situațiilor prevăzute de lege sau atunci când este necesar pentru furnizarea serviciilor (de exemplu, partajarea datelor cu medicii afiliați).</p>
    <h3>5. Securitatea Datelor</h3>
    <p>Implementăm măsuri tehnice și organizatorice adecvate pentru a proteja datele personale împotriva accesului neautorizat, pierderii, distrugerii sau alterării.</p>
    <h3>6. Drepturile Utilizatorilor</h3>
    <p>Utilizatorii au următoarele drepturi în conformitate cu GDPR:</p>
    <ul>
      <li>Dreptul de acces la datele personale.</li>
      <li>Dreptul de rectificare a datelor inexacte.</li>
      <li>Dreptul de ștergere a datelor (dreptul de a fi uitat).</li>
      <li>Dreptul de a restricționa prelucrarea.</li>
      <li>Dreptul de portabilitate a datelor.</li>
      <li>Dreptul de opoziție la prelucrarea datelor.</li>
      <li>Dreptul de a retrage consimțământul în orice moment.</li>
    </ul>
    <h3>7. Perioada de Stocare a Datelor</h3>
    <p>Datele personale sunt stocate doar pe perioada necesară îndeplinirii scopurilor pentru care au fost colectate, în conformitate cu prevederile legale aplicabile.</p>
    <h3>8. Contact</h3>
    <p>Pentru orice întrebări sau solicitări legate de protecția datelor personale, vă rugăm să ne contactați la:</p>
    <p>E-mail: support@medfm.com</p>
    <p>Telefon: +40 123 456 789</p>
    <h3>9. Modificări ale Politicii de Confidențialitate</h3>
    <p>Ne rezervăm dreptul de a modifica această politică de confidențialitate în orice moment. Orice modificări vor fi publicate pe această pagină și, dacă este cazul, vor fi comunicate utilizatorilor prin e-mail.</p>
  </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
  // Get the modal
  var modal = document.getElementById("gdprModal");

  // Get the link that opens the modal
  var link = document.getElementById("gdpr-link");

  // Get the <span> element that closes the modal
  var span = modal.getElementsByClassName("close")[0];

  // When the user clicks on the link, open the modal
  link.onclick = function(event) {
    event.preventDefault();
    modal.style.display = "block";
  }

  // When the user clicks on <span> (x), close the modal
  span.onclick = function() {
    modal.style.display = "none";
  }

  // When the user clicks anywhere outside of the modal, close it
  window.onclick = function(event) {
    if (event.target == modal) {
      modal.style.display = "none";
    }
  }
});
</script>
