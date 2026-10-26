const idAula = params.get("id");
let ACTUAL = null;
let SEGUIDOS = { ids: new Set(), conNota: new Set() };   // clases que sigue el alumno (vienen del servidor)
const DIAS = ["", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo"];
if (!params.has("dia") && hoy === 7) dia = 1;   // domingo: sin actividad, se muestra el lunes
const diaPedido = Number(params.get("d"));   // ?d=3 abre el aula en ese día (lo usan el buscador y los avisos)
if (diaPedido >= 1 && diaPedido <= 6) dia = diaPedido;

function pintar(aula, edificio, ed) {
  document.getElementById("titulo").textContent = `${edificio.nombre} - ${aula.etiqueta}`;
  document.title = `UNM Aulas · ${edificio.nombre} ${aula.etiqueta}`;
  document.getElementById("volver").href = `edificio.html?id=${ed}${params.has("dia") ? "&dia=" + dia : ""}${params.has("hora") ? "&hora=" + params.get("hora") : ""}`;
  ACTUAL = aula;
  pintarDias();
  pintarLinea(aula);
}

function pintarDias() {
  document.getElementById("dias").innerHTML = [1, 2, 3, 4, 5, 6].map(d =>
    `<button type="button" data-dia="${d}" aria-pressed="${d === dia}" aria-label="${DIAS[d]}${d === hoy ? " (hoy)" : ""}">${"LMXJVS"[d - 1]}${d === hoy ? "<i></i>" : ""}</button>`).join("");
  document.getElementById("dia").textContent = DIAS[dia] + (dia === hoy ? " · hoy" : "");
}

function pintarLinea(aula) {
  const clases = (aula.horarios || []).filter(h => h.dia === dia);
  let html = "";
  const cierre = dia === 6 ? H_FIN_SAB : H_FIN;
  for (let h = H_INICIO; h < cierre; h++) {
    const fila = h - H_INICIO + 1;
    html += `<div class="hora" style="grid-row:${fila}">${pad(h)}:00</div>`;
    const delHora = clases.filter(x => horaDe(x.inicio) === h);
    const c = delHora.find(x => !x.cancelada) || delHora[0];   // si hay una cancelada y otra que ocupa el lugar, se muestra la que ocupa
    if (c) {
      const dur = Math.max(1, horaFin(c.fin) - h);
      const ids = c.ids || [];
      const activo = ids.some(i => SEGUIDOS.ids.has(i));
      const conNota = ids.some(i => SEGUIDOS.conNota.has(i));
      const extra = c.cancelada ? " cancelada" : c.cambio ? " cambio" : c.evento ? " evento" : "";
      const detalle = c.cancelada ? (c.nota || "Cancelada") : [c.docente, c.comision].filter(Boolean).join(" | ");
      const pie = c.cancelada ? "" : (c.nota || c.carrera || (c.evento ? "Evento" : ""));
      const campanita = (c.cancelada || c.evento) ? "" :
        `<button class="campana-clase" type="button" data-ids="${ids.join(",")}" data-nota="${conNota ? 1 : 0}" aria-pressed="${activo}" aria-label="Recordatorio: ${c.materia}">${campana}</button>`;
      html += `<div class="clase ${dur === 1 ? "corta" : ""}${extra}" style="grid-row:${fila} / span ${dur};grid-column:2">
        <strong>${c.materia}</strong><span>${detalle}</span><em>${pie}</em>${campanita}</div>`;
    } else if (!clases.some(x => horaDe(x.inicio) < h && horaFin(x.fin) > h)) {
      html += `<div class="libre-franja" style="grid-row:${fila};grid-column:2"></div>`;
    }
  }
  document.getElementById("tiempo").innerHTML = html;
}

document.getElementById("dias").addEventListener("click", e => {
  const b = e.target.closest("button[data-dia]");
  if (!b || !ACTUAL) return;
  dia = Number(b.dataset.dia);
  pintarDias();
  pintarLinea(ACTUAL);
});

document.getElementById("tiempo").addEventListener("click", e => {
  const b = e.target.closest(".campana-clase");
  if (!b) return;
  const ids = b.dataset.ids.split(",").map(Number);
  alternarRecordatorio(b, b.dataset.nota === "1").then(estado => {
    if (estado === null) return;   // se canceló o falló
    ids.forEach(i => { if (estado) SEGUIDOS.ids.add(i); else { SEGUIDOS.ids.delete(i); SEGUIDOS.conNota.delete(i); } });
  });
});

Promise.all([fetch("api/edificios.php").then(r => r.json()), fetch("api/aulas.php").then(r => r.json()), cargarSeguidos()]).then(([eds, aulas, seguidos]) => {
  SEGUIDOS = seguidos;
  for (const ed in aulas) for (const p of aulas[ed].pisos) {
    const aula = p.aulas.find(a => a.id === idAula);
    if (aula) return pintar(aula, eds.find(e => e.id === ed), ed);
  }
  document.getElementById("tiempo").innerHTML = '<p class="vacio">Aula no encontrada.</p>';
}).catch(() => { document.getElementById("tiempo").innerHTML = '<p class="vacio">No se pudieron cargar los datos. Abrí la app desde localhost.</p>'; });
