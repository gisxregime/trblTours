.tour-request-overlay {
    position: fixed;
    inset: 0;
    z-index: 70;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 18px;
    background: rgba(63, 45, 34, 0.6);
    backdrop-filter: blur(7px);
    -webkit-backdrop-filter: blur(7px);
    opacity: 0;
    visibility: hidden;
    pointer-events: none;
    transition: opacity 0.22s ease, visibility 0.22s ease;
}

.tour-request-overlay.is-open {
    opacity: 1;
    visibility: visible;
    pointer-events: auto;
}

.tour-request-modal {
    width: min(700px, 95%);
    max-height: min(88vh, 860px);
    overflow-y: auto;
    border-radius: var(--radius-xl, 24px);
    border: 1px solid #eadfcd;
    background: var(--white, #ffffff);
    box-shadow: 0 24px 56px rgba(63, 45, 34, 0.32);
    padding: 22px;
    transform: translateY(12px) scale(0.98);
    transition: transform 0.24s ease;
}

.tour-request-overlay.is-open .tour-request-modal {
    transform: translateY(0) scale(1);
}

.tour-request-title {
    color: var(--brown-900, #3f2d22);
    font-size: clamp(1.4rem, 2vw, 1.8rem);
    font-weight: 700;
    line-height: 1.25;
}

.tour-request-subtitle {
    margin-top: 4px;
    font-size: 13px;
    color: #7a6a58;
}

.tour-request-form {
    margin-top: 16px;
    display: grid;
    gap: 12px;
}

.tour-request-grid-2 {
    display: grid;
    gap: 12px;
    grid-template-columns: repeat(2, minmax(0, 1fr));
}

.tour-request-field {
    display: grid;
    gap: 6px;
}

.tour-request-label {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    color: #6b5a49;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}

.tour-request-control {
    width: 100%;
    min-height: 44px;
    border-radius: var(--radius-lg, 16px);
    border: 1px solid #deceb7;
    background: #fffcf6;
    color: #4f4134;
    font-size: 14px;
    padding: 10px 12px;
    outline: none;
    transition: border-color 0.16s ease, box-shadow 0.16s ease;
}

.tour-request-control:focus {
    border-color: #b9915f;
    box-shadow: 0 0 0 3px rgba(185, 145, 95, 0.2);
}

.tour-request-control::placeholder {
    color: #a08f7c;
}

.tour-request-textarea {
    min-height: 120px;
    resize: vertical;
}

.tour-request-chip-wrap {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.tour-request-chip {
    border-radius: 999px;
    border: 1px solid #d8c7ad;
    background: #fff7ea;
    color: #5f503f;
    font-size: 12px;
    font-weight: 600;
    padding: 7px 12px;
    transition: all 0.18s ease;
}

.tour-request-chip:hover {
    border-color: #b9915f;
    color: #4f4134;
}

.tour-request-chip.is-selected {
    border-color: #6f8e52;
    background: #edf4e6;
    color: #385032;
}

.tour-request-feedback {
    display: none;
    border-radius: 12px;
    padding: 10px 12px;
    font-size: 13px;
    line-height: 1.4;
}

.tour-request-feedback.is-error {
    display: block;
    border: 1px solid #f2b8b1;
    background: #fdeeed;
    color: #8d2d21;
}

.tour-request-feedback.is-success {
    display: block;
    border: 1px solid #bcd9b8;
    background: #edf8ec;
    color: #2e5b2a;
}

.tour-request-actions {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    padding-top: 6px;
}

.tour-request-cancel {
    border: 1px solid transparent;
    background: transparent;
    color: var(--brown-700, #6f5d52);
    font-size: 14px;
    font-weight: 700;
    padding: 10px 14px;
    border-radius: var(--radius-lg, 16px);
    transition: background-color 0.16s ease;
}

.tour-request-cancel:hover {
    background: #f6efe3;
}

.tour-request-submit {
    border: 1px solid var(--brown-800, #5a4a39);
    background: var(--brown-800, #5a4a39);
    color: var(--white, #ffffff);
    font-size: 14px;
    font-weight: 700;
    padding: 10px 18px;
    border-radius: var(--radius-lg, 16px);
    transition: background-color 0.16s ease, transform 0.16s ease;
}

.tour-request-submit:hover:not(:disabled) {
    background: #4a3b33;
    transform: translateY(-1px);
}

.tour-request-submit:disabled {
    opacity: 0.65;
    cursor: not-allowed;
}

@media (max-width: 768px) {
    .tour-request-modal {
        padding: 16px;
    }

    .tour-request-grid-2 {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 560px) {
    .tour-request-actions {
        flex-direction: column;
    }

    .tour-request-cancel,
    .tour-request-submit {
        width: 100%;
    }
}
<?php /**PATH /home/mistah-regime/tribaltours/resources/views/partials/tour-request-modal-styles.blade.php ENDPATH**/ ?>