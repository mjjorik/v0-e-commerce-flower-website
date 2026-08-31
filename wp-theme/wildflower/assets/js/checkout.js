(function ($) {
  'use strict';

  var config = window.wildflowerCheckout || {};
  var messages = config.messages || {};
  var expressServerReady = false;
  var expressSyncTimer = null;
  var expressSyncRequest = 0;

  function checkoutForm() {
    return document.querySelector('form.checkout');
  }

  function shippingAddressActive() {
    var address = document.querySelector('form.checkout .shipping_address');
    return Boolean(address && (address.offsetParent !== null || address.getClientRects().length));
  }

  function pickupSelected() {
    var method = document.querySelector('input[name^="shipping_method"]:checked');
    if (!method) {
      method = document.querySelector('input[name^="shipping_method"][type="hidden"]');
    }
    return Boolean(method && String(method.value).indexOf('local_pickup') !== -1);
  }

  function fieldRow(fieldId) {
    return document.getElementById(fieldId + '_field');
  }

  function clearFieldError(fieldId) {
    var row = fieldRow(fieldId);
    var field = document.getElementById(fieldId);
    if (!row || !field) {
      return;
    }

    row.classList.remove('woocommerce-invalid', 'woocommerce-invalid-required-field');
    row.classList.add('woocommerce-validated');
    field.setAttribute('aria-invalid', 'false');
    row.querySelectorAll('.wf-checkout-field-error').forEach(function (error) {
      error.remove();
    });
  }

  function showFieldError(fieldId, message) {
    var row = fieldRow(fieldId);
    var field = document.getElementById(fieldId);
    if (!row || !field) {
      return;
    }

    row.classList.remove('woocommerce-validated');
    row.classList.add('woocommerce-invalid', 'woocommerce-invalid-required-field');
    field.setAttribute('aria-invalid', 'true');

    var error = row.querySelector('.wf-checkout-field-error');
    if (!error) {
      error = document.createElement('span');
      error.className = 'wf-checkout-field-error';
      error.setAttribute('role', 'alert');
      var wrapper = row.querySelector('.woocommerce-input-wrapper') || row;
      wrapper.appendChild(error);
    }
    error.textContent = message;
  }

  function morningRestricted(dateValue) {
    if (config.sameDayAllowed && (!dateValue || dateValue === config.earliestDate)) {
      return true;
    }
    return Boolean(config.afterNoon && dateValue === config.earliestDate);
  }

  function updateWindowOptions() {
    var date = document.getElementById('wildflower_delivery_date');
    var windowSelect = document.getElementById('wildflower_delivery_window');
    if (!date || !windowSelect) {
      return;
    }

    var morning = windowSelect.querySelector('option[value="morning"]');
    var restricted = morningRestricted(date.value);
    if (morning) {
      morning.disabled = restricted;
    }
    if (restricted && windowSelect.value === 'morning') {
      windowSelect.value = '';
    }
  }

  function validateDeliveryFields() {
    if (pickupSelected()) {
      clearFieldError('wildflower_delivery_date');
      clearFieldError('wildflower_delivery_window');
      return true;
    }

    var date = document.getElementById('wildflower_delivery_date');
    var windowSelect = document.getElementById('wildflower_delivery_window');
    var valid = true;

    if (!date || !date.value) {
      showFieldError('wildflower_delivery_date', messages.dateRequired || 'Delivery date is required.');
      valid = false;
    } else {
      clearFieldError('wildflower_delivery_date');
    }

    if (!windowSelect || !windowSelect.value) {
      showFieldError('wildflower_delivery_window', messages.windowRequired || 'Please select a preferred delivery window.');
      valid = false;
    } else if (morningRestricted(date ? date.value : '') && windowSelect.value === 'morning') {
      showFieldError('wildflower_delivery_window', messages.windowCutoff || 'Morning delivery is unavailable for this date.');
      valid = false;
    } else {
      clearFieldError('wildflower_delivery_window');
    }

    return valid;
  }

  function validateChangedDeliveryField(target) {
    if (pickupSelected()) {
      clearFieldError('wildflower_delivery_date');
      clearFieldError('wildflower_delivery_window');
      return;
    }

    var date = document.getElementById('wildflower_delivery_date');
    var windowSelect = document.getElementById('wildflower_delivery_window');

    if (target && target.id === 'wildflower_delivery_date') {
      if (date && date.value) {
        clearFieldError('wildflower_delivery_date');
      }
      if (date && windowSelect && windowSelect.value) {
        if (morningRestricted(date.value) && windowSelect.value === 'morning') {
          showFieldError('wildflower_delivery_window', messages.windowCutoff || 'Morning delivery is unavailable for this date.');
        } else {
          clearFieldError('wildflower_delivery_window');
        }
      }
      return;
    }

    if (target && target.id === 'wildflower_delivery_window') {
      if (!windowSelect || !windowSelect.value) {
        showFieldError('wildflower_delivery_window', messages.windowRequired || 'Please select a preferred delivery window.');
      } else if (morningRestricted(date ? date.value : '') && windowSelect.value === 'morning') {
        showFieldError('wildflower_delivery_window', messages.windowCutoff || 'Morning delivery is unavailable for this date.');
      } else {
        clearFieldError('wildflower_delivery_window');
      }
    }
  }

  function positionDatepicker() {
    var input = document.getElementById('wildflower_delivery_date');
    var picker = document.getElementById('ui-datepicker-div');
    if (!input || !picker || picker.style.display === 'none') {
      return;
    }

    var inputBox = input.getBoundingClientRect();
    var pickerBox = picker.getBoundingClientRect();
    var pageTop = window.scrollY || document.documentElement.scrollTop;
    var pageLeft = window.scrollX || document.documentElement.scrollLeft;
    var gutter = 8;
    var viewportTop = pageTop + gutter;
    var viewportBottom = pageTop + window.innerHeight - gutter;
    var below = pageTop + inputBox.bottom + gutter;
    var above = pageTop + inputBox.top - pickerBox.height - gutter;
    var top = below;

    if (below + pickerBox.height > viewportBottom && above >= viewportTop) {
      top = above;
    }
    top = Math.max(viewportTop, Math.min(top, viewportBottom - pickerBox.height));

    var left = pageLeft + inputBox.left;
    var maxLeft = pageLeft + window.innerWidth - pickerBox.width - gutter;
    left = Math.max(pageLeft + gutter, Math.min(left, maxLeft));

    picker.style.top = Math.round(top) + 'px';
    picker.style.left = Math.round(left) + 'px';
  }

  function scheduleDatepickerPosition() {
    window.setTimeout(positionDatepicker, 0);
  }

  function setupDatepicker() {
    var date = $('#wildflower_delivery_date');
    if (!date.length) {
      return;
    }

    date.attr({
      min: config.earliestDate || '',
      autocomplete: 'off',
      inputmode: 'none',
      readonly: 'readonly'
    });

    if (date.val() && config.earliestDate && date.val() < config.earliestDate) {
      date.val('');
      showFieldError('wildflower_delivery_date', messages.dateRequired || 'Delivery date is required.');
    }

    if ($.fn.datepicker && !date.hasClass('hasDatepicker')) {
      date.datepicker({
        dateFormat: 'yy-mm-dd',
        minDate: Number(config.minNoticeDays || 0),
        beforeShow: scheduleDatepickerPosition,
        onChangeMonthYear: scheduleDatepickerPosition,
        beforeShowDay: function (candidate) {
          if (!config.earliestDate) {
            return [true, '', ''];
          }
          var earliest = $.datepicker.parseDate('yy-mm-dd', config.earliestDate);
          return [candidate >= earliest, '', ''];
        },
        onSelect: function () {
          updateWindowOptions();
          date.trigger('change');
        }
      });
    }
  }

  function updateDeliveryFields() {
    var isPickup = pickupSelected();
    var fieldIds = ['wildflower_delivery_date', 'wildflower_delivery_window'];
    var section = document.getElementById('wf_delivery_fields');

    setupDatepicker();
    updateWindowOptions();

    fieldIds.forEach(function (fieldId) {
      var row = fieldRow(fieldId);
      var field = document.getElementById(fieldId);
      if (!row || !field) {
        return;
      }
      row.classList.toggle('validate-required', !isPickup);
      field.required = !isPickup;
      var requiredMark = row.querySelector('.required');
      if (requiredMark) {
        requiredMark.hidden = isPickup;
      }
    });

    if (section) {
      section.classList.toggle('is-pickup', isPickup);
      var title = section.querySelector('h3');
      if (title) {
        title.textContent = isPickup ? 'Pickup details' : 'Delivery details';
      }
    }

    if (isPickup) {
      clearFieldError('wildflower_delivery_date');
      clearFieldError('wildflower_delivery_window');
    }
  }

  function requiredFieldsReady() {
    var form = checkoutForm();
    if (!form) {
      return false;
    }

    if (!pickupSelected()) {
      var date = document.getElementById('wildflower_delivery_date');
      var windowSelect = document.getElementById('wildflower_delivery_window');
      if (!date || !date.value || !windowSelect || !windowSelect.value) {
        return false;
      }
      if (morningRestricted(date.value) && windowSelect.value === 'morning') {
        return false;
      }
    }

    var shippingActive = shippingAddressActive();
    var rows = Array.prototype.slice.call(form.querySelectorAll('.validate-required'));
    return rows.every(function (row) {
      if (!shippingActive && row.closest('.shipping_address')) {
        return true;
      }
      if (row.offsetParent === null && !row.getClientRects().length) {
        return true;
      }

      var fields = Array.prototype.slice.call(row.querySelectorAll('input:not([type="hidden"]), select, textarea')).filter(function (field) {
        return !field.disabled && field.type !== 'button' && field.type !== 'submit';
      });
      if (!fields.length) {
        return true;
      }

      return fields.some(function (field) {
        if (field.type === 'checkbox' || field.type === 'radio') {
          return field.checked;
        }
        var value = String(field.value || '').trim();
        if (!value) {
          return false;
        }
        if (field.type === 'email') {
          return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
        }
        return true;
      });
    });
  }

  function moveExpressCheckout() {
    var form = checkoutForm();
    var review = document.getElementById('order_review');
    var express = form ? form.querySelector('.wcpay-express-checkout-wrapper') : null;
    if (!review || !express) {
      return null;
    }

    var payment = review.querySelector('#payment');
    express.classList.add('wf-express-checkout');

    if (!express.querySelector('.wf-express-required-note')) {
      var note = document.createElement('p');
      note.className = 'wf-express-required-note';
      note.textContent = messages.expressReady || 'Complete the required checkout fields above to use express checkout.';
      express.appendChild(note);
    }

    if (!express.dataset.wfGuardBound) {
      express.addEventListener('click', function (event) {
        if (express.classList.contains('wf-express-disabled')) {
          event.preventDefault();
          event.stopImmediatePropagation();
        }
      }, true);
      express.dataset.wfGuardBound = 'true';
    }

    var separator = express.querySelector('#wcpay-express-checkout-button-separator');
    if (separator) {
      separator.textContent = 'Express checkout';
    }

    if (payment && express.nextElementSibling !== payment) {
      review.insertBefore(express, payment);
    } else if (!payment && express.parentElement !== review) {
      review.appendChild(express);
    }

    return express;
  }

  function updateExpressControls(express, ready) {
    express.querySelectorAll('a, button, [tabindex]').forEach(function (control) {
      if (!ready) {
        if (!Object.prototype.hasOwnProperty.call(control.dataset, 'wfOriginalTabindex')) {
          control.dataset.wfOriginalTabindex = control.hasAttribute('tabindex') ? control.getAttribute('tabindex') : '__none__';
        }
        control.setAttribute('tabindex', '-1');
        control.setAttribute('aria-disabled', 'true');
        return;
      }

      if (control.dataset.wfOriginalTabindex === '__none__') {
        control.removeAttribute('tabindex');
      } else if (Object.prototype.hasOwnProperty.call(control.dataset, 'wfOriginalTabindex')) {
        control.setAttribute('tabindex', control.dataset.wfOriginalTabindex);
      }
      control.removeAttribute('aria-disabled');
      delete control.dataset.wfOriginalTabindex;
    });
  }

  function setExpressNote(message) {
    var note = document.querySelector('.wf-express-required-note');
    if (note) {
      note.textContent = message || messages.expressReady || 'Complete the required checkout fields above to use express checkout.';
    }
  }

  function updateExpressAvailability() {
    var express = moveExpressCheckout();
    if (!express) {
      return;
    }

    var ready = requiredFieldsReady() && expressServerReady;
    express.classList.toggle('wf-express-disabled', !ready);
    express.setAttribute('aria-disabled', ready ? 'false' : 'true');
    express.dataset.wfRequiredReady = ready ? 'true' : 'false';
    updateExpressControls(express, ready);
  }

  function syncExpressCheckout() {
    var form = checkoutForm();
    var express = moveExpressCheckout();
    var requestId = ++expressSyncRequest;

    expressServerReady = false;
    updateExpressAvailability();

    if (!form || !express || !config.ajaxUrl || !config.nonce || !requiredFieldsReady()) {
      setExpressNote();
      return;
    }

    $.ajax({
      url: config.ajaxUrl,
      method: 'POST',
      dataType: 'json',
      data: {
        security: config.nonce,
        form_data: $(form).serialize(),
        shipping_address_active: shippingAddressActive() ? 'yes' : 'no'
      }
    }).done(function (response) {
      if (requestId !== expressSyncRequest) {
        return;
      }
      expressServerReady = Boolean(response && response.success && response.data && response.data.ready);
      setExpressNote(expressServerReady ? '' : response && response.data && response.data.message);
      updateExpressAvailability();
    }).fail(function (xhr) {
      if (requestId !== expressSyncRequest) {
        return;
      }
      expressServerReady = false;
      var response = xhr && xhr.responseJSON;
      setExpressNote(response && response.data && response.data.message);
      updateExpressAvailability();
    });
  }

  function scheduleExpressSync(delay) {
    expressServerReady = false;
    updateExpressAvailability();
    window.clearTimeout(expressSyncTimer);
    expressSyncTimer = window.setTimeout(syncExpressCheckout, typeof delay === 'number' ? delay : 250);
  }

  $(function () {
    if (!checkoutForm()) {
      return;
    }

    setupDatepicker();
    updateDeliveryFields();
    moveExpressCheckout();
    updateExpressAvailability();

    [120, 360, 900, 1800, 3200].forEach(function (delay) {
      window.setTimeout(function () {
        moveExpressCheckout();
        updateExpressAvailability();
      }, delay);
    });

    document.addEventListener('input', function (event) {
      var form = checkoutForm();
      if (form && form.contains(event.target)) {
        scheduleExpressSync();
      }
    }, true);

    document.addEventListener('change', function (event) {
      var form = checkoutForm();
      if (!form || !form.contains(event.target)) {
        return;
      }
      if (event.target.matches('#wildflower_delivery_date, #wildflower_delivery_window, input[name^="shipping_method"]')) {
        updateDeliveryFields();
        validateChangedDeliveryField(event.target);
      }
      scheduleExpressSync();
    }, true);

    $(document.body).on('updated_checkout', function () {
      updateDeliveryFields();
      scheduleExpressSync(100);
      window.setTimeout(moveExpressCheckout, 80);
      window.setTimeout(moveExpressCheckout, 320);
    });

    $('form.checkout').on('checkout_place_order', function () {
      updateWindowOptions();
      return validateDeliveryFields();
    });

    scheduleExpressSync(150);
  });
})(jQuery);
