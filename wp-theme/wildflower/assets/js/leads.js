(function () {
  'use strict';

  var config = window.wildflowerLeads || {};

  function setStatus(status, state, message) {
    if (!status) return;
    status.classList.remove('is-success', 'is-error');
    if (state) status.classList.add('is-' + state);
    status.textContent = message || '';
    if (state === 'error') status.focus();
  }

  function markInvalidFields(form, fields) {
    fields.forEach(function (name) {
      var field = form.elements.namedItem(name);
      if (field && typeof field.setAttribute === 'function') {
        field.setAttribute('aria-invalid', 'true');
      }
    });
  }

  function clearInvalidField(event) {
    if (event.target && event.target.getAttribute('aria-invalid') === 'true') {
      event.target.removeAttribute('aria-invalid');
    }
  }

  function responseMessage(payload, fallback) {
    if (payload && payload.data && typeof payload.data.message === 'string') {
      return payload.data.message;
    }
    return fallback;
  }

  document.querySelectorAll('[data-wildflower-lead-form]').forEach(function (form) {
    var status = form.querySelector('[data-wildflower-form-status]');
    var button = form.querySelector('button[type="submit"]');

    form.addEventListener('input', clearInvalidField);
    form.addEventListener('change', clearInvalidField);

    form.addEventListener('submit', function (event) {
      event.preventDefault();

      if (!form.reportValidity()) return;
      if (!config.ajaxUrl || !config.nonce) {
        setStatus(status, 'error', 'Messaging is temporarily unavailable. Please call or contact us on WhatsApp.');
        return;
      }

      var data = new FormData(form);
      data.set('action', 'wildflower_submit_lead');
      data.set('nonce', config.nonce);
      data.set('source', form.getAttribute('data-lead-source') || '');

      setStatus(status, '', '');
      if (button) {
        button.disabled = true;
        button.setAttribute('aria-busy', 'true');
      }

      fetch(config.ajaxUrl, {
        method: 'POST',
        credentials: 'same-origin',
        body: data,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      })
        .then(function (response) {
          return response.json().catch(function () { return null; }).then(function (payload) {
            return { ok: response.ok, payload: payload };
          });
        })
        .then(function (result) {
          if (!result.ok || !result.payload || result.payload.success !== true) {
            if (result.payload && result.payload.data && Array.isArray(result.payload.data.fields)) {
              markInvalidFields(form, result.payload.data.fields);
            }
            throw new Error(responseMessage(result.payload, 'Your request could not be sent. Please try again or contact us on WhatsApp.'));
          }

          form.reset();
          setStatus(status, 'success', responseMessage(result.payload, 'Thank you. Your request has been sent to our studio team.'));
        })
        .catch(function (error) {
          setStatus(status, 'error', error.message || 'Your request could not be sent. Please try again or contact us on WhatsApp.');
        })
        .finally(function () {
          if (button) {
            button.disabled = false;
            button.removeAttribute('aria-busy');
          }
        });
    });
  });
})();
