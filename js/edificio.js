const idEdificio = params.get("id") || "daract2";

let filtro = "todas";
let vista = sessionStorage.getItem("vista") || "grilla";
let pisos = [];
const cont = document.getElementById("contenido");
const btnVista = document.getElementById("vista");
const iconoCompu = '<svg class="icono-compu" viewBox="0 0 24 24" role="img" aria-label="Aula de computación"><path d="M3 4h18a1 1 0 011 1v11a1 1 0 01-1 1h-7v2h3v2H7v-2h3v-2H3a1 1 0 01-1-1V5a1 1 0 011-1zm1 2v9h16V6z"/></svg>';
const iconoLista = '<i class="ico ico-lista" aria-hidden="true"></i>';
const iconoGrilla = '<i class="ico ico-grilla" aria-hidden="true"></i>';

function pintar() {
  btnVista.innerHTML = vista === "grilla" ? iconoLista : iconoGrilla;
  btnVista.setAttribute("aria-label", vista === "grilla" ? "Ver como lista" : "Ver como grilla");
  let total = 0;
  const html = pisos.map(p => {
    const items = p.aulas.map(a => ({ a, clase: claseActual(a) })).filter(({ a, clase }) =>
      a.tipo === "info" ? filtro === "todas" : filtro === "todas" || (filtro === "ocupadas") === !!clase);
    if (!items.length) return "";
    total += items.length;
    const tarjetas = items.map(({ a, clase }) => {
      const href = `aula.html?id=${a.id}&e=${idEdificio}${params.has('dia') ? '&dia=' + dia : ''}${params.has('hora') ? '&hora=' + hora : ''}`;
      if (a.tipo === "info") return vista === "grilla"      // bloque informativo (ej. Biblioteca): solo horario, sin enlace
        ? `<div class="aula sum"><b>${a.etiqueta}</b><small>${a.horarioTexto}</small></div>`
        : `<div class="fila sum"><b>${a.etiqueta}</b><span>${a.horarioTexto}</span></div>`;
      const dot = `<i class="dot ${clase ? "ocupada" : "libre"}" role="img" aria-label="${clase ? "Ocupada" : "Libre"}"></i>`;
      if (a.tipo === "sum") {
        const prox = (a.horarios || []).filter(h => h.dia === dia && !h.cancelada && aMin(h.inicio) > aMin(hora)).sort((x, y) => aMin(x.inicio) - aMin(y.inicio))[0];
        const info = clase ? `${clase.inicio} - ${clase.fin} · ${clase.materia}` : prox ? `Próximo: ${prox.inicio} · ${prox.materia}` : a.detalle;
        return vista === "grilla"
          ? `<a class="aula sum" href="${href}"><b>${a.etiqueta}</b>${dot}<small>${info}</small></a>`
          : `<a class="fila sum" href="${href}"><b>${a.etiqueta}</b><span>${info}</span>${dot}</a>`;
      }
      return vista === "grilla"
        ? `<a class="aula ${a.ancho === 2 ? "ancha" : ""}" href="${href}"><b>${a.etiqueta}</b>${dot}<span class="info">${clase ? `<strong>${clase.materia}</strong><small>${clase.inicio} - ${clase.fin}</small>` : ""}</span>${a.compu ? iconoCompu : ""}</a>`
        : `<a class="fila" href="${href}"><b>${a.etiqueta}</b><span>${clase ? clase.materia : ""}</span>${a.compu ? iconoCompu : ""}${dot}</a>`;
    }).join("");
    return `<section class="piso"><h2>${p.nombre}</h2><div class="${vista === "grilla" ? "grilla" : "lista"}">${tarjetas}</div></section>`;
  }).join("");
  cont.innerHTML = total ? html : '<p class="vacio">No hay aulas con ese estado ahora.</p>';
}

document.querySelectorAll(".filtros button").forEach(b => b.addEventListener("click", () => {
  filtro = b.dataset.filtro;
  document.querySelectorAll(".filtros button").forEach(x => x.setAttribute("aria-pressed", x === b));
  pintar();
}));
btnVista.addEventListener("click", () => { vista = vista === "grilla" ? "lista" : "grilla"; sessionStorage.setItem("vista", vista); pintar(); });

Promise.all([fetch("api/edificios.php").then(r => r.json()), fetch("api/aulas.php").then(r => r.json())]).then(([eds, aulas]) => {
  const ed = eds.find(e => e.id === idEdificio);
  if (!ed) { cont.innerHTML = '<p class="vacio">Edificio no encontrado.</p>'; return; }
  document.getElementById("titulo").textContent = ed.nombre;
  document.title = `UNM Aulas · ${ed.nombre}`;
  document.getElementById("fondo").style.backgroundImage = `url('${ed.foto}'), linear-gradient(135deg, ${ed.color[0]}, ${ed.color[1]})`;
  if (!aulas[idEdificio]) { cont.innerHTML = '<p class="vacio">Las aulas de este edificio se cargan próximamente.</p>'; return; }
  pisos = aulas[idEdificio].pisos;
  pintar();
}).catch(() => { cont.innerHTML = '<p class="vacio">No se pudieron cargar los datos. Abrí la app desde localhost.</p>'; });
