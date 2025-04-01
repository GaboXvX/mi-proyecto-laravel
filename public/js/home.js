//limitar caracteres
document.getElementById('cedula').addEventListener('input', function(e) {
    if (this.value.length > 8) {
        this.value = this.value.slice(0, 8); 
    }
  });
  
  document.getElementById('nombreUsuario').addEventListener('input', function(e) {
    if (this.value.length > 20) {
        this.value = this.value.slice(0, 20);
    }
  });
  
  document.getElementById('nombre').addEventListener('input', function(e) {
    if (this.value.length > 11) {
        this.value = this.value.slice(0, 11); 
    }
  });
  
  document.getElementById('apellido').addEventListener('input', function(e) {
    if (this.value.length > 11) {
        this.value = this.value.slice(0, 11); 
    }
});

//visualizar contraseña
function showEye() {
  const passwordField = document.getElementById("password");
  const toggleIcon = document.getElementById("toggleIcon");
  if (passwordField.value.length > 0) {
      toggleIcon.style.display = "block";
  } else {
      toggleIcon.style.display = "none";
  }
}

function togglePassword() {
  const passwordField = document.getElementById("password");
  const toggleIcon = document.getElementById("toggleIcon");
  if (passwordField.type === "password") {
      passwordField.type = "text";
      toggleIcon.classList.remove("bi-eye-slash");
      toggleIcon.classList.add("bi-eye");
  } else {
      passwordField.type = "password";
      toggleIcon.classList.remove("bi-eye");
      toggleIcon.classList.add("bi-eye-slash");
  }
}