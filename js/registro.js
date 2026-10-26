// Registro real: crea la cuenta en la base con api/registro.php (contraseña encriptada, DNI único).
const $ = id => document.getElementById(id);

fetch("api/carreras.php").then(r => r.json()).then(lista => {
  $("carrera").insertAdjacentHTML("beforeend", lista.map(c => `<option value="${c.id}">${c.nombre}</option>`).join(""));
}).catch(() => { $("error").textContent = "No se pudieron cargar las carreras. Abrí la app desde localhost."; });

let enCurso = false;
$("form-registro").addEventListener("submit", async e => {
  e.preventDefault();
  if (enCurso) return;
  const v = { dni: $("dni").value.trim(), nombre: $("nombre").value.trim(), apellido: $("apellido").value.trim(),
              carrera: $("carrera").value, clave: $("clave").value };
  const falla = (() => {
    if (!/^\d{7,8}$/.test(v.dni)) return "El DNI debe tener 7 u 8 números, sin puntos.";
    if (!v.nombre || !v.apellido) return "Completá nombre y apellido.";
    if (!v.carrera) return "Elegí tu carrera.";
    if (v.clave.length < 8) return "La contraseña debe tener al menos 8 caracteres.";
    if (v.clave !== $("clave2").value) return "Las contraseñas no coinciden.";
    return "";
  })();
  if (falla) { $("error").textContent = falla; return; }
  $("error").textContent = "";

  enCurso = true;
  try {
    const r = await fetch("api/registro.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ dni: v.dni, nombre: v.nombre, apellido: v.apellido, carrera: Number(v.carrera), clave: v.clave })
    });
    const d = await r.json().catch(() => ({}));
    if (!r.ok) { $("error").textContent = d.error || "No se pudo crear la cuenta. Probá de nuevo."; return; }
    const u = d.usuario;
    sessionStorage.setItem("sesion", JSON.stringify({ id: u.id, nombre: u.nombre, carrera: u.carrera, rol: u.rol }));
    location.href = "inicio.html";
  } catch (err) {
    $("error").textContent = "No se pudo conectar con el servidor. Abrí la app desde localhost.";
  } finally {
    enCurso = false;
  }
});

// Ojito: muestra u oculta la contraseña del campo indicado
document.querySelectorAll("[data-ojo]").forEach(b => b.addEventListener("click", () => {
  const campo = $(b.dataset.ojo);
  const ver = campo.type === "password";
  campo.type = ver ? "text" : "password";
  b.setAttribute("aria-label", ver ? "Ocultar contraseña" : "Mostrar contraseña");
}));
