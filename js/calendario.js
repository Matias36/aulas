const MESES = ["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"];
const MESES_CORTO = ["Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic"];
const SEMANA = ["lun", "mar", "mié", "jue", "vie", "sáb", "dom"];
const clave = d => `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;

const fechaHoy = new Date();
let anio = fechaHoy.getFullYear(), mes = fechaHoy.getMonth();
let seleccionado = null;     // "YYYY-MM-DD" o null
let eventos = [];                 // cada uno: { fecha, fin (o null si dura un día), titulo }
let diasConEvento = new Set();    // todos los días que tienen algo (los eventos de varios días cuentan cada día)

function indexar() {
  diasConEvento = new Set();
  for (const e of eventos) {
    const [y, m, d] = e.fecha.split("-").map(Number);
    const ultimo = e.fin || e.fecha;
    const dia = new Date(y, m - 1, d);
    for (let i = 0; i < 400 && clave(dia) <= ultimo; i++) { diasConEvento.add(clave(dia)); dia.setDate(dia.getDate() + 1); }
  }
}

// "24 mar 2026"  ·  "16 – 17 feb 2026"  ·  "20 mar – 11 abr 2026"
function etiqueta(e) {
  const [y, m, d] = e.fecha.split("-").map(Number);
  if (!e.fin || e.fin === e.fecha) return `${pad(d)} ${MESES_CORTO[m - 1]} ${y}`;
  const [y2, m2, d2] = e.fin.split("-").map(Number);
  if (y === y2 && m === m2) return `${pad(d)} – ${pad(d2)} ${MESES_CORTO[m - 1]} ${y}`;
  if (y === y2) return `${pad(d)} ${MESES_CORTO[m - 1]} – ${pad(d2)} ${MESES_CORTO[m2 - 1]} ${y}`;
  return `${pad(d)} ${MESES_CORTO[m - 1]} ${y} – ${pad(d2)} ${MESES_CORTO[m2 - 1]} ${y2}`;
}


function pintar() {
  document.getElementById("mes").textContent = `${MESES[mes]} ${anio}`;
  const primero = new Date(anio, mes, 1);
  const desfase = (primero.getDay() + 6) % 7;            // la semana empieza en lunes
  const conEvento = diasConEvento;
  let html = SEMANA.map(s => `<div class="sem">${s}</div>`).join("");
  for (let i = 0; i < 42; i++) {
    const d = new Date(anio, mes, 1 - desfase + i);
    const k = clave(d);
    const cls = ["d", d.getMonth() !== mes ? "otro" : "", d.getDay() === 0 ? "dom" : "", k === clave(fechaHoy) ? "hoy" : "", k === seleccionado ? "sel" : ""].join(" ");
    html += `<button type="button" class="${cls}" data-k="${k}" aria-label="${d.getDate()} de ${MESES[d.getMonth()]}${conEvento.has(k) ? ", con evento" : ""}">${d.getDate()}${conEvento.has(k) ? "<i></i>" : ""}</button>`;
  }
  document.getElementById("cal").innerHTML = html;

  const primeroMes = `${anio}-${pad(mes + 1)}-01`, ultimoMes = `${anio}-${pad(mes + 1)}-31`;
  const lista = eventos
    .filter(e => {
      const ultimo = e.fin || e.fecha;
      return seleccionado ? e.fecha <= seleccionado && ultimo >= seleccionado     // los que abarcan el día tocado
                          : e.fecha <= ultimoMes && ultimo >= primeroMes;          // los que tocan el mes visible
    })
    .sort((a, b) => a.fecha.localeCompare(b.fecha) || a.titulo.localeCompare(b.titulo));
  document.getElementById("lista").innerHTML = lista.length ? lista.map(e => {
    return `<li class="fecha"><small>${etiqueta(e)}</small><strong>${e.titulo}</strong></li>`;
  }).join("") : '<li class="vacio">No hay fechas importantes para mostrar.</li>';
}

const cambiarMes = n => { const d = new Date(anio, mes + n, 1); anio = d.getFullYear(); mes = d.getMonth(); seleccionado = null; pintar(); };
document.getElementById("prev").addEventListener("click", () => cambiarMes(-1));
document.getElementById("next").addEventListener("click", () => cambiarMes(1));

document.getElementById("cal").addEventListener("click", e => {
  const b = e.target.closest(".d");
  if (!b) return;
  seleccionado = seleccionado === b.dataset.k ? null : b.dataset.k;   // tocar de nuevo quita el filtro
  pintar();
});

fetch("api/eventos.php").then(r => r.json()).then(d => { eventos = d; indexar(); pintar(); })
  .catch(() => { pintar(); document.getElementById("lista").innerHTML = '<li class="vacio">No se pudieron cargar las fechas. Abrí la app desde localhost.</li>'; });
