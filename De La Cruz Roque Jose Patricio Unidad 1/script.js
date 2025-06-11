// Funcionalidad del menú móvil
document.addEventListener("DOMContentLoaded", () => {
  const mobileMenuBtn = document.getElementById("mobile-menu-btn")
  const mobileMenu = document.getElementById("mobile-menu")

  if (mobileMenuBtn && mobileMenu) {
    mobileMenuBtn.addEventListener("click", () => {
      mobileMenu.classList.toggle("hidden")
      mobileMenu.classList.toggle("slide-down")
    })
  }

  setupRealTimeValidation()

  const forms = document.querySelectorAll("form")
  forms.forEach((form) => {
    form.addEventListener("submit", function (e) {
      e.preventDefault()

      if (!validateForm(this)) {
        alert("Por favor, corrige los errores en el formulario.")
        return
      }

      const formData = new FormData(this)
      const captchaResponse = grecaptcha.getResponse()
      formData.append("g-recaptcha-response", captchaResponse)

      const action = this.getAttribute("id") === "login-form" ? "login.php" : "registrar.php"

      console.log("Enviando a:", action)

      fetch(action, {
        method: "POST",
        body: formData
      })
        .then((res) => {
          if (!res.ok) throw new Error("HTTP status " + res.status)
          return res.text()
        })
              .then((data) => {
                 data = data.trim();
        console.log("Respuesta del servidor:", data)

        if (action === "login.php") {
          switch (data) {
            case "admin":
            case "usuario":
              window.location.href = "index.html"
              break
            case "usuario_no_encontrado":
              alert("Este usuario no está registrado.")
              break
            case "contrasena_incorrecta":
              alert("La contraseña es incorrecta.")
              break
            default:
              alert("Error desconocido durante el login.")
          }
        } else if (action === "registrar.php") {
          switch (data) {
            case "correo_existente":
              alert("Este correo ya está registrado. Intenta con otro.")
              break
            case "recaptcha_faltante":
              alert("Por favor, completa el reCAPTCHA.")
              break
            case "recaptcha_invalido":
              alert("No pudimos verificar que eres humano. Intenta nuevamente.")
              break
            case "registro_exitoso":
              alert("¡Registro exitoso!")
              form.reset()
              const inputs = form.querySelectorAll("input")
              inputs.forEach((input) => clearValidation(input))
              if (typeof grecaptcha !== 'undefined') grecaptcha.reset()
              break
            default:
              alert("Ocurrió un error al registrar. Inténtalo de nuevo.")
          }
        }
      })

        .catch((error) => {
          console.error("Error capturado:", error)
          alert("Error de red o del servidor.")
        })
    })
  })
})

// Validaciones

function validateEmail(email) {
  const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  return re.test(email)
}

function validatePassword(password) {
  return password.length >= 6
}

function validateRequired(value) {
  return value.trim() !== ""
}

function showError(input, message) {
  input.classList.add("error")
  input.classList.remove("success")

  const existingError = input.parentNode.querySelector(".error-message")
  if (existingError) {
    existingError.remove()
  }

  const errorDiv = document.createElement("div")
  errorDiv.className = "error-message"
  errorDiv.textContent = message
  input.parentNode.appendChild(errorDiv)
}

function showSuccess(input) {
  input.classList.add("success")
  input.classList.remove("error")

  const existingError = input.parentNode.querySelector(".error-message")
  if (existingError) {
    existingError.remove()
  }
}

function clearValidation(input) {
  input.classList.remove("error", "success")
  const existingError = input.parentNode.querySelector(".error-message")
  if (existingError) {
    existingError.remove()
  }
}

function setupRealTimeValidation() {
  const inputs = document.querySelectorAll('input[required], input[type="email"], input[type="password"]')

  inputs.forEach((input) => {
    input.addEventListener("blur", function () {
      validateInput(this)
    })

    input.addEventListener("input", function () {
      if (this.classList.contains("error")) {
        validateInput(this)
      }
    })
  })
}

function validateInput(input) {
  const value = input.value
  const type = input.type
  const required = input.hasAttribute("required")

  clearValidation(input)

  if (required && !validateRequired(value)) {
    showError(input, "Este campo es obligatorio")
    return false
  }

  if (type === "email" && value && !validateEmail(value)) {
    showError(input, "Ingresa un email válido")
    return false
  }

  if (type === "password" && value && !validatePassword(value)) {
    showError(input, "La contraseña debe tener al menos 6 caracteres")
    return false
  }

  if (input.name === "confirm_password") {
    const passwordInput = document.querySelector('input[name="password"]')
    if (passwordInput && value !== passwordInput.value) {
      showError(input, "Las contraseñas no coinciden")
      return false
    }
  }

  if (value) {
    showSuccess(input)
  }
  return true
}

function validateForm(form) {
  const inputs = form.querySelectorAll('input[required], input[type="email"], input[type="password"]')
  let isValid = true

  inputs.forEach((input) => {
    if (!validateInput(input)) {
      isValid = false
    }
  })

  return isValid
}

// Mostrar/ocultar contraseña
function togglePassword(buttonId, inputId) {
  const button = document.getElementById(buttonId)
  const input = document.getElementById(inputId)

  if (button && input) {
    button.addEventListener("click", function () {
      const type = input.getAttribute("type") === "password" ? "text" : "password"
      input.setAttribute("type", type)

      const icon = this.querySelector("svg")
      if (type === "password") {
        icon.innerHTML =
          '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>'
      } else {
        icon.innerHTML =
          '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>'
      }
    })
  }
}
