// Login real: valida contra api/login.php (la sesión queda en el servidor).
const error = document.getElementById("error");
const pass = document.getElementById("password");
const ojo = document.getElementById("ojo");

// Si venía de otra pantalla (comun.js lo manda al login con ?volver=...), vuelve ahí después de ingresar.
// Solo se aceptan nombres de página de esta misma carpeta.
const volver = new URLSearchParams(location.search).get("volver") || "";
const destinoSeguro = /^[a-z0-9_-]+\.html(\?[A-Za-z0-9_=&%.:\-]*)?$/i.test(volver) && !/^(index|crear-cuenta)\.html/i.test(volver) ? volver : "";

ojo.addEventListener("click", () => {
  const oculto = pass.type === "password";
  pass.type = oculto ? "text" : "password";
  ojo.setAttribute("aria-label", oculto ? "Ocultar contraseña" : "Mostrar contraseña");
});

// Dos modos en la misma pantalla: ingreso de alumnos y ingreso de administrador
let modoAdmin = false;
function ponerModo(admin) {
  modoAdmin = admin;
  const titulo = document.getElementById("titulo");
  titulo.textContent = admin ? "Administrador" : "Bienvenido";
  titulo.style.fontSize = admin ? "34px" : "";
  document.getElementById("dni").placeholder = admin ? "DNI de administrador." : "DNI.";
  document.getElementById("btn-acceder").textContent = admin ? "Ingresar como administrador" : "Acceder";
  document.getElementById("btn-olvido").hidden = admin;
  document.getElementById("btn-crear").hidden = admin;
  document.getElementById("btn-admin").textContent = admin ? "Volver al ingreso de alumnos" : "Acceso administrador";
  error.textContent = "";
  pass.value = "";
}

let enCurso = false;
async function ingresar(esAdmin) {
  if (enCurso) return;
  error.textContent = "";
  const dni = document.getElementById("dni").value.trim();
  const clave = pass.value;
  if (!dni || !clave) { error.textContent = "Completá tu DNI y la contraseña."; return; }
  if (!/^\d+$/.test(dni)) { error.textContent = "El DNI solo lleva números."; return; }
  enCurso = true;
  try {
    const r = await fetch("api/login.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ dni, clave, admin: esAdmin })
    });
    const d = await r.json().catch(() => ({}));
    if (!r.ok) { error.textContent = d.error || "No se pudo ingresar. Probá de nuevo."; return; }
    const u = d.usuario;
    sessionStorage.setItem("sesion", JSON.stringify({ id: u.id, nombre: u.nombre, carrera: u.carrera, rol: u.rol }));
    location.href = u.rol === "administrador" ? "admin.html" : (destinoSeguro || "inicio.html"); // admin.html: pantalla pendiente
  } catch (e) {
    error.textContent = "No se pudo conectar con el servidor. Abrí la app desde localhost, no con doble clic.";
  } finally {
    enCurso = false;
  }
}

document.getElementById("form-login").addEventListener("submit", e => { e.preventDefault(); ingresar(modoAdmin); });
document.getElementById("btn-admin").addEventListener("click", () => ponerModo(!modoAdmin));
document.getElementById("btn-crear").addEventListener("click", () => { location.href = "crear-cuenta.html"; });
document.getElementById("btn-olvido").addEventListener("click", () => alert("Recuperar contraseña: pantalla pendiente."));

// Solo se aceptan números en el campo DNI
document.getElementById("dni").addEventListener("input", e => { e.target.value = e.target.value.replace(/\D/g, ""); });
