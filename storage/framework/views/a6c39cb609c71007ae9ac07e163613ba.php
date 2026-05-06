<script>
    (() => {
        const overlay = document.getElementById('tourRequestModal');
        const form = document.getElementById('tourRequestForm');
        const feedback = document.getElementById('tourRequestFeedback');

        if (!overlay || !form || !feedback) {
            return;
        }

        const titleInput = document.getElementById('tourRequestInputTitle');
        const durationInput = document.getElementById('tourRequestDuration');
        const budgetInput = document.getElementById('tourRequestBudget');
        const regionInput = document.getElementById('tourRequestRegion');
        const locationInput = document.getElementById('tourRequestLocation');
        const adultsInput = document.getElementById('tourRequestAdults');
        const childrenInput = document.getElementById('tourRequestChildren');
        const dateInput = document.getElementById('tourRequestDate');
        const descriptionInput = document.getElementById('tourRequestDescription');
        const interestsInput = document.getElementById('tourRequestInterests');
        const chipsWrap = document.getElementById('tourRequestChips');
        const addInterestButton = document.getElementById('tourRequestAddInterest');
        const submitButton = form.querySelector('.tour-request-submit');

        const todayString = new Date().toISOString().split('T')[0];
        dateInput.min = todayString;

        const selectedInterests = new Set();

        const syncInterests = () => {
            interestsInput.value = Array.from(selectedInterests).join(', ');
        };

        const resetFeedback = () => {
            feedback.className = 'tour-request-feedback';
            feedback.textContent = '';
        };

        const setFeedback = (message, type = 'error') => {
            feedback.className = `tour-request-feedback ${type === 'success' ? 'is-success' : 'is-error'}`;
            feedback.textContent = message;
        };

        const setModalOpen = (isOpen) => {
            overlay.classList.toggle('is-open', isOpen);
            overlay.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
            document.body.style.overflow = isOpen ? 'hidden' : '';

            if (isOpen) {
                window.setTimeout(() => {
                    titleInput?.focus();
                }, 80);
            }
        };

        document.addEventListener('click', (event) => {
            const triggerButton = event.target.closest('#openRequestModal');
            if (triggerButton) {
                event.preventDefault();
                resetFeedback();
                setModalOpen(true);
                return;
            }

            const closeButton = event.target.closest('[data-close-tour-request]');
            if (closeButton) {
                event.preventDefault();
                setModalOpen(false);
            }
        });

        overlay.addEventListener('click', (event) => {
            if (event.target === overlay) {
                setModalOpen(false);
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && overlay.classList.contains('is-open')) {
                setModalOpen(false);
            }
        });

        chipsWrap?.addEventListener('click', (event) => {
            const chip = event.target.closest('[data-interest-chip]');
            if (!chip) {
                return;
            }

            const value = chip.getAttribute('data-interest-chip');
            if (!value) {
                return;
            }

            if (selectedInterests.has(value)) {
                selectedInterests.delete(value);
                chip.classList.remove('is-selected');
            } else {
                selectedInterests.add(value);
                chip.classList.add('is-selected');
            }

            syncInterests();
        });

        addInterestButton?.addEventListener('click', () => {
            const customInterest = window.prompt('Add custom interest');
            const normalized = customInterest?.trim();

            if (!normalized || selectedInterests.has(normalized)) {
                return;
            }

            selectedInterests.add(normalized);

            const customChip = document.createElement('button');
            customChip.type = 'button';
            customChip.className = 'tour-request-chip is-selected';
            customChip.setAttribute('data-interest-chip', normalized);
            customChip.textContent = normalized;

            addInterestButton.before(customChip);
            syncInterests();
        });

        form.addEventListener('submit', async (event) => {
            event.preventDefault();
            resetFeedback();

            const title = titleInput.value.trim();
            const cityLocation = locationInput.value.trim();
            const region = regionInput.value.trim();
            const description = descriptionInput.value.trim();
            const duration = durationInput.value.trim();

            const adults = Math.max(0, Number.parseInt(adultsInput.value || '0', 10) || 0);
            const children = Math.max(0, Number.parseInt(childrenInput.value || '0', 10) || 0);
            const passengerCount = adults + children;

            const rawBudget = (budgetInput.value || '').replace(/[^\d.]/g, '');
            const budgetValue = Number.parseFloat(rawBudget);

            if (!title) {
                setFeedback('Request title is required.');
                titleInput.focus();
                return;
            }

            if (!cityLocation && !region) {
                setFeedback('Please provide a region or city/province destination.');
                locationInput.focus();
                return;
            }

            if (!Number.isFinite(budgetValue) || budgetValue < 500) {
                setFeedback('Budget must be at least PHP 500.');
                budgetInput.focus();
                return;
            }

            if (passengerCount < 1) {
                setFeedback('At least 1 traveler is required.');
                adultsInput.focus();
                return;
            }

            if (!description) {
                setFeedback('Description is required.');
                descriptionInput.focus();
                return;
            }

            const selectedDate = dateInput.value || todayString;
            const location = [region, cityLocation].filter(Boolean).join(' - ');

            let finalDescription = description;
            const detailLines = [];

            if (duration) {
                detailLines.push(`Duration: ${duration}`);
            }

            if (interestsInput.value.trim()) {
                detailLines.push(`Interests: ${interestsInput.value.trim()}`);
            }

            if (detailLines.length > 0) {
                finalDescription = `${description}\n\n${detailLines.join('\n')}`;
            }

            const formData = new FormData();
            formData.set('title', title);
            formData.set('location', location);
            formData.set('preferred_date', selectedDate);
            formData.set('passenger_count', String(passengerCount));
            formData.set('budget_min', budgetValue.toFixed(2));
            formData.set('budget_max', budgetValue.toFixed(2));
            formData.set('description', finalDescription);
            formData.set('duration', duration);
            formData.set('region', region);
            formData.set('adults', String(adults));
            formData.set('children', String(children));
            formData.set('interests', interestsInput.value.trim());

            submitButton.disabled = true;
            submitButton.textContent = 'Submitting...';

            try {
                const response = await fetch("<?php echo e(route('requests.store')); ?>", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': "<?php echo e(csrf_token()); ?>",
                        'Accept': 'application/json',
                    },
                    body: formData,
                });

                const payload = await response.json().catch(() => ({}));

                if (!response.ok) {
                    const errorMessage = payload?.message
                        || (payload?.errors ? Object.values(payload.errors).flat().join(' ') : null)
                        || 'Unable to submit request right now. Please check your fields and try again.';
                    setFeedback(errorMessage);
                    return;
                }

                setFeedback(payload?.message || 'Request submitted successfully.', 'success');

                form.reset();
                adultsInput.value = '1';
                childrenInput.value = '0';
                selectedInterests.clear();
                chipsWrap.querySelectorAll('[data-interest-chip]').forEach((chip) => {
                    chip.classList.remove('is-selected');
                });
                syncInterests();

                window.setTimeout(() => {
                    setModalOpen(false);
                    resetFeedback();
                }, 900);
            } catch (error) {
                setFeedback('Unable to submit request right now. Please try again.');
            } finally {
                submitButton.disabled = false;
                submitButton.textContent = 'Post';
            }
        });
    })();
</script>
<?php /**PATH /home/mistah-regime/tribaltours/resources/views/partials/tour-request-modal-script.blade.php ENDPATH**/ ?>