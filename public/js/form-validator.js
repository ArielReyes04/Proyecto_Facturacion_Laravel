/**
 * Validaciones del lado del cliente para formularios
 */

class FormValidator {
    constructor() {
        this.initializeValidators();
    }

    initializeValidators() {
        // Validación en tiempo real para email
        this.setupEmailValidation();
        
        // Validación en tiempo real para documentos
        this.setupDocumentValidation();
        
        // Validación en tiempo real para contraseñas
        this.setupPasswordValidation();
        
        // Validación de nombres similares
        this.setupNameValidation();
    }

    setupEmailValidation() {
        const emailInputs = document.querySelectorAll('input[type="email"]');
        
        emailInputs.forEach(input => {
            input.addEventListener('blur', this.validateEmail.bind(this));
            input.addEventListener('input', this.clearEmailErrors.bind(this));
        });
    }

    setupDocumentValidation() {
        const documentTypeSelect = document.getElementById('document_type');
        const documentNumberInput = document.getElementById('document_number');
        
        if (documentTypeSelect && documentNumberInput) {
            documentTypeSelect.addEventListener('change', () => {
                this.updateDocumentFormat(documentTypeSelect.value, documentNumberInput);
            });
            
            documentNumberInput.addEventListener('input', this.validateDocumentNumber.bind(this));
            documentNumberInput.addEventListener('blur', this.validateDocumentNumber.bind(this));
        }
    }

    setupPasswordValidation() {
        const passwordInput = document.getElementById('password');
        const passwordConfirmInput = document.getElementById('password_confirmation');
        
        if (passwordInput) {
            passwordInput.addEventListener('input', this.validatePassword.bind(this));
        }
        
        if (passwordConfirmInput) {
            passwordConfirmInput.addEventListener('input', this.validatePasswordConfirmation.bind(this));
        }
    }

    setupNameValidation() {
        const nameInput = document.getElementById('name');
        
        if (nameInput) {
            let debounceTimer;
            nameInput.addEventListener('input', (e) => {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    this.checkSimilarNames(e.target.value);
                }, 500);
            });
        }
    }

    validateEmail(event) {
        const input = event.target;
        const email = input.value.trim();
        
        this.clearFieldErrors(input);
        
        if (!email) return;
        
        // Validación básica de formato
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            this.showFieldError(input, 'El formato del email no es válido.');
            return;
        }
        
        // Verificar disponibilidad (solo si no estamos editando)
        if (!this.isEditMode()) {
            this.checkEmailAvailability(email, input);
        }
    }

    clearEmailErrors(event) {
        this.clearFieldErrors(event.target);
    }

    updateDocumentFormat(documentType, input) {
        this.clearFieldErrors(input);
        
        let placeholder = '';
        let maxLength = 20;
        
        switch (documentType) {
            case 'DNI':
                placeholder = 'Ingrese 8 dígitos';
                maxLength = 8;
                break;
            case 'RUC':
                placeholder = 'Ingrese 11 dígitos';
                maxLength = 11;
                break;
            case 'CE':
                placeholder = 'Ingrese 9 dígitos';
                maxLength = 9;
                break;
            case 'Pasaporte':
                placeholder = 'Ingrese 6-12 caracteres alfanuméricos';
                maxLength = 12;
                break;
            default:
                placeholder = 'Seleccione un tipo de documento';
        }
        
        input.placeholder = placeholder;
        input.maxLength = maxLength;
    }

    validateDocumentNumber(event) {
        const input = event.target;
        const documentType = document.getElementById('document_type')?.value;
        const value = input.value.trim();
        
        this.clearFieldErrors(input);
        
        if (!value || !documentType) return;
        
        let isValid = false;
        let errorMessage = '';
        
        switch (documentType) {
            case 'DNI':
                isValid = /^\d{8}$/.test(value);
                errorMessage = 'El DNI debe tener exactamente 8 dígitos.';
                break;
            case 'RUC':
                isValid = /^\d{11}$/.test(value);
                errorMessage = 'El RUC debe tener exactamente 11 dígitos.';
                break;
            case 'CE':
                isValid = /^\d{9}$/.test(value);
                errorMessage = 'El Carné de Extranjería debe tener exactamente 9 dígitos.';
                break;
            case 'Pasaporte':
                isValid = /^[A-Z0-9]{6,12}$/i.test(value);
                errorMessage = 'El Pasaporte debe tener entre 6 y 12 caracteres alfanuméricos.';
                break;
        }
        
        if (!isValid) {
            this.showFieldError(input, errorMessage);
        }
    }

    validatePassword(event) {
        const input = event.target;
        const password = input.value;
        
        this.clearFieldErrors(input);
        
        if (!password) return;
        
        const requirements = {
            length: password.length >= 8,
            lowercase: /[a-z]/.test(password),
            uppercase: /[A-Z]/.test(password),
            number: /\d/.test(password),
            symbol: /[@$!%*?&]/.test(password)
        };
        
        const errors = [];
        
        if (!requirements.length) errors.push('al menos 8 caracteres');
        if (!requirements.lowercase) errors.push('una minúscula');
        if (!requirements.uppercase) errors.push('una mayúscula');
        if (!requirements.number) errors.push('un número');
        if (!requirements.symbol) errors.push('un símbolo (@$!%*?&)');
        
        if (errors.length > 0) {
            this.showFieldError(input, `La contraseña debe contener: ${errors.join(', ')}.`);
        }
        
        // Actualizar indicador visual
        this.updatePasswordStrength(requirements);
    }

    validatePasswordConfirmation(event) {
        const input = event.target;
        const confirmation = input.value;
        const password = document.getElementById('password')?.value;
        
        this.clearFieldErrors(input);
        
        if (!confirmation) return;
        
        if (password !== confirmation) {
            this.showFieldError(input, 'Las contraseñas no coinciden.');
        }
    }

    updatePasswordStrength(requirements) {
        const strengthIndicator = document.getElementById('password-strength');
        if (!strengthIndicator) return;
        
        const score = Object.values(requirements).filter(Boolean).length;
        const maxScore = Object.keys(requirements).length;
        
        let strength = '';
        let colorClass = '';
        
        if (score === maxScore) {
            strength = 'Fuerte';
            colorClass = 'text-green-600';
        } else if (score >= 3) {
            strength = 'Media';
            colorClass = 'text-yellow-600';
        } else if (score >= 1) {
            strength = 'Débil';
            colorClass = 'text-red-600';
        } else {
            strength = 'Muy débil';
            colorClass = 'text-red-700';
        }
        
        strengthIndicator.textContent = `Fortaleza: ${strength}`;
        strengthIndicator.className = `text-sm ${colorClass}`;
    }

    async checkEmailAvailability(email, input) {
        try {
            const response = await fetch('/check-email-availability', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ email })
            });
            
            const data = await response.json();
            
            if (!data.available) {
                this.showFieldError(input, data.message || 'Este email ya está en uso.');
            }
        } catch (error) {
            console.error('Error checking email availability:', error);
        }
    }

    async checkSimilarNames(name) {
        if (name.length < 3) return;
        
        try {
            const modelType = this.getModelType();
            const response = await fetch('/check-similar-names', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ name, model: modelType })
            });
            
            const data = await response.json();
            
            if (data.similar_names && data.similar_names.length > 0) {
                this.showSimilarNamesWarning(data.similar_names);
            } else {
                this.hideSimilarNamesWarning();
            }
        } catch (error) {
            console.error('Error checking similar names:', error);
        }
    }

    showFieldError(input, message) {
        // Agregar clase de error al campo
        input.classList.add('border-red-500', 'focus:border-red-500');
        
        // Crear o actualizar mensaje de error
        const errorId = `${input.id}-error`;
        let errorElement = document.getElementById(errorId);
        
        if (!errorElement) {
            errorElement = document.createElement('div');
            errorElement.id = errorId;
            errorElement.className = 'mt-1 text-sm text-red-600';
            input.parentNode.insertBefore(errorElement, input.nextSibling);
        }
        
        errorElement.textContent = message;
    }

    clearFieldErrors(input) {
        // Remover clases de error
        input.classList.remove('border-red-500', 'focus:border-red-500');
        
        // Remover mensaje de error
        const errorId = `${input.id}-error`;
        const errorElement = document.getElementById(errorId);
        if (errorElement) {
            errorElement.remove();
        }
    }

    showSimilarNamesWarning(names) {
        const warningId = 'similar-names-warning';
        let warningElement = document.getElementById(warningId);
        
        if (!warningElement) {
            warningElement = document.createElement('div');
            warningElement.id = warningId;
            warningElement.className = 'mt-2 p-3 bg-yellow-50 border border-yellow-200 rounded-md';
            
            const nameInput = document.getElementById('name');
            nameInput.parentNode.insertBefore(warningElement, nameInput.nextSibling);
        }
        
        warningElement.innerHTML = `
            <div class="flex">
                <i class="fas fa-exclamation-triangle text-yellow-400 mr-2"></i>
                <div>
                    <p class="text-sm text-yellow-800 font-medium">Nombres similares encontrados:</p>
                    <ul class="mt-1 text-sm text-yellow-700">
                        ${names.map(name => `<li>• ${name}</li>`).join('')}
                    </ul>
                    <p class="mt-1 text-xs text-yellow-600">¿Está seguro de que no es el mismo registro?</p>
                </div>
            </div>
        `;
    }

    hideSimilarNamesWarning() {
        const warningElement = document.getElementById('similar-names-warning');
        if (warningElement) {
            warningElement.remove();
        }
    }

    getModelType() {
        // Determinar el tipo de modelo basado en la URL o contexto
        if (window.location.pathname.includes('/users')) {
            return 'User';
        } else if (window.location.pathname.includes('/clients')) {
            return 'Client';
        }
        return 'User'; // Por defecto
    }

    isEditMode() {
        // Verificar si estamos en modo edición
        return window.location.pathname.includes('/edit') || 
               document.querySelector('input[name="_method"][value="PUT"]') !== null;
    }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    new FormValidator();
});

// Funciones de utilidad para otros scripts
window.FormValidatorUtils = {
    showError: function(fieldId, message) {
        const input = document.getElementById(fieldId);
        if (input) {
            const validator = new FormValidator();
            validator.showFieldError(input, message);
        }
    },
    
    clearErrors: function(fieldId) {
        const input = document.getElementById(fieldId);
        if (input) {
            const validator = new FormValidator();
            validator.clearFieldErrors(input);
        }
    }
};
