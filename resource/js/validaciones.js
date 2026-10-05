/**
 * FormValidation - Sistema de validación de formularios estilo Laravel
 *
 * Uso: FormValidation.init('#miFormulario')
 *
 * Atributos HTML soportados:
 *   required             - Campo obligatorio
 *   type="email"         - Valida formato email
 *   type="tel"           - Valida formato teléfono venezolano (04XX-XXXXXXX)
 *   data-minlength="N"   - Mínimo N caracteres
 *   data-maxlength="N"   - Máximo N caracteres
 *   data-alphanumeric    - Solo letras y números (mínimo 8 caracteres)
 *   data-match="id"      - Debe coincidir con el campo referenciado
 *   data-pattern="rx"    - Expresión regular personalizada
 *   data-pattern-msg=""  - Mensaje para data-pattern
 *   data-validator="fn"  - Función custom en window.FormValidators
 *   data-custom-msg=""   - Mensaje para validación custom
 */
const FormValidation = (function() {
    var touched = new Set();

    var defaultMessages = {
        required: 'Este campo es obligatorio',
        email: 'Ingrese un correo electrónico válido',
        tel: 'Formato: 04XX-XXXXXXX (11 dígitos)',
        minlength: function(n) { return 'Mínimo ' + n + ' caracteres'; },
        maxlength: function(n) { return 'Máximo ' + n + ' caracteres'; },
        alphanumeric: 'Mínimo 8 caracteres, solo letras y números',
        match: 'Las contraseñas no coinciden',
        pattern: 'Formato inválido',
        custom: 'Valor inválido'
    };

    var validators = {
        required: function(value) {
            return value.trim() !== '';
        },
        email: function(value) {
            if (value.trim() === '') return true;
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value.trim());
        },
        tel: function(value) {
            if (value.trim() === '') return true;
            var digits = value.replace(/[^0-9]/g, '');
            return digits.length === 11 && digits.startsWith('04');
        },
        minlength: function(value, min) {
            if (value.trim() === '') return true;
            return value.trim().length >= parseInt(min);
        },
        maxlength: function(value, max) {
            if (value.trim() === '') return true;
            return value.trim().length <= parseInt(max);
        },
        alphanumeric: function(value) {
            if (value.trim() === '') return true;
            return /^[A-Za-z0-9]+$/.test(value.trim()) && value.trim().length >= 8;
        },
        match: function(value, targetId) {
            if (value.trim() === '') return true;
            var target = document.getElementById(targetId);
            if (!target) return true;
            return value.trim() === target.value.trim();
        },
        pattern: function(value, pattern) {
            if (value.trim() === '') return true;
            return new RegExp(pattern).test(value.trim());
        }
    };

    function getMessage(campo, tipo) {
        var customMsg = campo.getAttribute('data-custom-msg');
        if (tipo === 'custom') return customMsg || defaultMessages.custom;
        if (tipo === 'minlength') return defaultMessages.minlength(campo.getAttribute('data-minlength'));
        if (tipo === 'maxlength') return defaultMessages.maxlength(campo.getAttribute('data-maxlength'));
        if (tipo === 'match') return defaultMessages.match;
        if (tipo === 'alphanumeric') return defaultMessages.alphanumeric;
        if (tipo === 'pattern') return campo.getAttribute('data-pattern-msg') || defaultMessages.pattern;
        return defaultMessages[tipo] || 'Valor inválido';
    }

    function getValidatorName(campo) {
        if (campo.hasAttribute('required') && campo.value.trim() === '') return 'required';
        if (campo.type === 'email') return 'email';
        if (campo.type === 'tel') return 'tel';
        if (campo.hasAttribute('data-alphanumeric')) return 'alphanumeric';
        if (campo.hasAttribute('data-match')) return 'match';
        if (campo.hasAttribute('data-minlength')) return 'minlength';
        if (campo.hasAttribute('data-maxlength')) return 'maxlength';
        if (campo.hasAttribute('data-pattern')) return 'pattern';
        if (campo.hasAttribute('data-validator')) return 'custom';
        return null;
    }

    function getFeedback(campo) {
        var group = campo.closest('.form-group');
        if (!group) return null;
        return group.querySelector('.invalid-feedback');
    }

    function showFeedback(campo, msg) {
        var feedback = getFeedback(campo);
        if (feedback) {
            feedback.textContent = msg;
            feedback.style.display = 'block';
        }
    }

    function hideFeedback(campo) {
        var feedback = getFeedback(campo);
        if (feedback) {
            feedback.style.display = 'none';
        }
    }

    function validate(campo) {
        var tipo = getValidatorName(campo);

        if (!tipo) {
            campo.classList.remove('is-invalid');
            hideFeedback(campo);
            return true;
        }

        var isValid = true;

        if (tipo === 'required') {
            isValid = validators.required(campo.value);
        } else if (tipo === 'email') {
            isValid = validators.email(campo.value);
        } else if (tipo === 'tel') {
            isValid = validators.tel(campo.value);
        } else if (tipo === 'minlength') {
            isValid = validators.minlength(campo.value, campo.getAttribute('data-minlength'));
        } else if (tipo === 'maxlength') {
            isValid = validators.maxlength(campo.value, campo.getAttribute('data-maxlength'));
        } else if (tipo === 'alphanumeric') {
            isValid = validators.alphanumeric(campo.value);
        } else if (tipo === 'match') {
            isValid = validators.match(campo.value, campo.getAttribute('data-match'));
        } else if (tipo === 'pattern') {
            isValid = validators.pattern(campo.value, campo.getAttribute('data-pattern'));
        } else if (tipo === 'custom') {
            var fnName = campo.getAttribute('data-validator');
            if (typeof window.FormValidators !== 'undefined' && typeof window.FormValidators[fnName] === 'function') {
                isValid = window.FormValidators[fnName](campo.value.trim());
            }
        }

        if (isValid) {
            campo.classList.remove('is-invalid');
            hideFeedback(campo);
        } else {
            campo.classList.add('is-invalid');
            showFeedback(campo, getMessage(campo, tipo));
        }

        return isValid;
    }

    function markTouched(campo) {
        touched.add(campo.name || campo.id || campo);
    }

    function isTouched(campo) {
        return touched.has(campo.name || campo.id || campo);
    }

    function isSkippable(campo) {
        return ['hidden', 'submit', 'button', 'file', 'reset', 'image'].indexOf(campo.type) !== -1;
    }

    function init(formSelector) {
        var form = document.querySelector(formSelector);
        if (!form) return;

        var fields = form.querySelectorAll('input, select, textarea');

        fields.forEach(function(campo) {
            if (isSkippable(campo)) return;

            campo.addEventListener('blur', function() {
                markTouched(this);
                validate(this);
            });

            campo.addEventListener('input', function() {
                if (isTouched(this)) {
                    validate(this);
                }

                if (this.hasAttribute('data-match')) {
                    var targetId = this.getAttribute('data-match');
                    var target = document.getElementById(targetId);
                    if (target && isTouched(target)) {
                        validate(target);
                    }
                }
            });

            if (campo.tagName === 'SELECT') {
                campo.addEventListener('change', function() {
                    markTouched(this);
                    validate(this);
                });
            }
        });

        form.addEventListener('submit', function(e) {
            var isValid = true;
            var firstInvalid = null;

            fields.forEach(function(campo) {
                if (isSkippable(campo)) return;

                markTouched(campo);
                if (!validate(campo)) {
                    isValid = false;
                    if (!firstInvalid) firstInvalid = campo;
                }
            });

            if (!isValid) {
                e.preventDefault();
                e.stopPropagation();
                if (firstInvalid) firstInvalid.focus();
            }
        });

        form.validate = function() {
            var isValid = true;
            fields.forEach(function(campo) {
                if (isSkippable(campo)) return;
                markTouched(campo);
                if (!validate(campo)) isValid = false;
            });
            return isValid;
        };
    }

    return {
        init: init,
        validate: validate,
        validators: validators
    };
})();
