const DIAS = ["", "Lun", "Mar", "Mié", "Jue", "Vie", "Sáb", "Dom"];

document.getElementById("nombre").textContent = sesion.nombre;
document.getElementById("carrera").textContent = sesion.carrera || "";
document.getElementById("avatar").textContent = sesion.nombre.split(" ").slice(0, 2).map(p => p[0]).join("").toUpperCase();

const lapiz = '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 17.3V21h3.7L18 9.7 14.3 6zM20.7 7a1 1 0 000-1.4l-2.3-2.3a1 1 0 00-1.4 0l-1.8 1.8 3.7 3.7z"/></svg>';
const ajustar = t => { t.style.height = "auto"; t.style.height = t.scrollHeight + "px"; };
const lista = document.getElementById("lista");
const sinCursos = '<li class="vacio">Todavía no seguís ninguna clase. Activá la campanita en un aula y aparece acá.</li>';

// "Mis cursos": las clases que el alumno sigue, guardadas en el servidor (api/seguimientos.php)
fetch("api/seguimientos.php", { cache: "no-store" }).then(r => {
  if (r.status === 401) { irAlLogin(); throw new Error("sin sesión"); }
  if (!r.ok) throw new Error("error");
  return r.json();
}).then(({ cursos }) => {
  if (!cursos.length) { lista.innerHTML = sinCursos; return; }
  lista.innerHTML = cursos.map(c => `<li class="curso">
      <div class="datos">
        <div class="cuando"><b>${DIAS[c.dia]}</b> ${c.inicio} - ${c.fin} &nbsp;|&nbsp; <b>${c.edificio}</b> ${c.aula}</div>
        <h3>${c.materia}</h3>
        <div class="docente">${[c.docente, c.comision].filter(Boolean).join(" | ")}</div>
        <span class="carrera">${c.carrera}</span>
        <button class="campana-curso" type="button" data-ids="${c.ids.join(",")}" aria-pressed="true" aria-label="Dejar de seguir: ${c.materia}">${campana}</button>
      </div>
      <label class="nota">${lapiz}<textarea rows="1" maxlength="500" data-ids="${c.ids.join(",")}" placeholder="Añadir nota." aria-label="Nota para ${c.materia}"></textarea></label>
    </li>`).join("");
  lista.querySelectorAll("textarea").forEach((t, i) => { t.value = cursos[i].nota || ""; t.dataset.guardada = t.value; ajustar(t); });
}).catch(e => { if (e.message !== "sin sesión") lista.innerHTML = '<li class="vacio">No se pudieron cargar los cursos. Abrí la app desde localhost.</li>'; });

// Las notas se guardan solas: un instante después de escribir, o al salir del campo
const temporizadores = {};
function enviarNota(t) {
  clearTimeout(temporizadores[t.dataset.ids]);
  if (t.value === t.dataset.guardada) return;
  const nota = t.value;
  fetch("api/seguimientos.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ accion: "nota", ids: t.dataset.ids.split(",").map(Number), nota }),
    keepalive: true
  }).then(r => { if (r.ok) t.dataset.guardada = nota; });
}

lista.addEventListener("input", e => {
  if (!e.target.matches("textarea")) return;
  ajustar(e.target);
  clearTimeout(temporizadores[e.target.dataset.ids]);
  temporizadores[e.target.dataset.ids] = setTimeout(() => enviarNota(e.target), 700);
});
lista.addEventListener("focusout", e => { if (e.target.matches("textarea")) enviarNota(e.target); });

// Desactivar la campanita desde Perfil quita la clase de "Mis cursos"
lista.addEventListener("click", async e => {
  const b = e.target.closest(".campana-curso");
  if (!b) return;
  const li = b.closest("li");
  const t = li.querySelector("textarea");
  const estado = await alternarRecordatorio(b, t.value.trim() !== "");
  if (estado !== false) return;
  clearTimeout(temporizadores[t.dataset.ids]);
  li.remove();
  if (!lista.querySelector(".curso")) lista.innerHTML = sinCursos;
});

document.getElementById("salir").addEventListener("click", cerrarSesion);
