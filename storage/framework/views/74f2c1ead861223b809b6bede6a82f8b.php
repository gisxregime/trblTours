<div id="tourRequestModal" class="tour-request-overlay" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="tourRequestTitle">
    <section class="tour-request-modal">
        <div class="flex items-start justify-between gap-3">
            <div>
                <h2 id="tourRequestTitle" class="tour-request-title">Create Your Request</h2>
                <p class="tour-request-subtitle">Tell guides what kind of trip you are planning and get matched faster.</p>
            </div>
            <button type="button" class="tour-request-cancel p-1" data-close-tour-request aria-label="Close request modal">
                <i class="fa-solid fa-xmark text-base"></i>
            </button>
        </div>

        <form id="tourRequestForm" class="tour-request-form" novalidate>
            <?php echo csrf_field(); ?>
            <input type="hidden" name="interests" id="tourRequestInterests">

            <div id="tourRequestFeedback" class="tour-request-feedback" aria-live="polite"></div>

            <div class="tour-request-field">
                <label class="tour-request-label" for="tourRequestInputTitle">
                    <i class="fa-solid fa-pen-to-square"></i>
                    <span>Request Title</span>
                </label>
                <input id="tourRequestInputTitle" type="text" class="tour-request-control" placeholder="Enter request title" required>
            </div>

            <div class="tour-request-grid-2">
                <div class="tour-request-field">
                    <label class="tour-request-label" for="tourRequestDuration">
                        <i class="fa-solid fa-clock"></i>
                        <span>Duration</span>
                    </label>
                    <input id="tourRequestDuration" type="text" class="tour-request-control" placeholder="3 Days">
                </div>
                <div class="tour-request-field">
                    <label class="tour-request-label" for="tourRequestBudget">
                        <i class="fa-solid fa-peso-sign"></i>
                        <span>Budget</span>
                    </label>
                    <input id="tourRequestBudget" type="text" inputmode="decimal" class="tour-request-control" placeholder="Php 5,000">
                </div>
            </div>

            <div class="tour-request-grid-2">
                <div class="tour-request-field">
                    <label class="tour-request-label" for="tourRequestRegion">
                        <i class="fa-solid fa-earth-asia"></i>
                        <span>Philippine Region</span>
                    </label>
                    <input id="tourRequestRegion" list="tourRequestRegionList" class="tour-request-control" placeholder="Search region">
                    <datalist id="tourRequestRegionList">
                        <option value="NCR - National Capital Region"></option>
                        <option value="CAR"></option>
                        <option value="Region I - Ilocos Region"></option>
                        <option value="Region II - Cagayan Valley"></option>
                        <option value="Region III - Central Luzon"></option>
                        <option value="Region IV-A - CALABARZON"></option>
                        <option value="Region IV-B - MIMAROPA"></option>
                        <option value="Region V - Bicol Region"></option>
                        <option value="Region VI - Western Visayas"></option>
                        <option value="Region VII - Central Visayas"></option>
                        <option value="Region VIII - Eastern Visayas"></option>
                        <option value="Region IX - Zamboanga Peninsula"></option>
                        <option value="Region X - Northern Mindanao"></option>
                        <option value="Region XI - Davao Region"></option>
                        <option value="Region XII - SOCCSKSARGEN"></option>
                        <option value="Region XIII - Caraga"></option>
                        <option value="BARMM"></option>
                    </datalist>
                </div>
                <div class="tour-request-field">
                    <label class="tour-request-label" for="tourRequestLocation">
                        <i class="fa-solid fa-location-dot"></i>
                        <span>Location / City / Province</span>
                    </label>
                    <input id="tourRequestLocation" type="text" class="tour-request-control" placeholder="Enter city, province, or destination" required>
                </div>
            </div>

            <div class="tour-request-grid-2">
                <div class="tour-request-field">
                    <label class="tour-request-label" for="tourRequestAdults">
                        <i class="fa-solid fa-users"></i>
                        <span>Adults</span>
                    </label>
                    <input id="tourRequestAdults" type="number" class="tour-request-control" min="0" max="20" value="1">
                </div>
                <div class="tour-request-field">
                    <label class="tour-request-label" for="tourRequestChildren">
                        <i class="fa-solid fa-child-reaching"></i>
                        <span>Children</span>
                    </label>
                    <input id="tourRequestChildren" type="number" class="tour-request-control" min="0" max="20" value="0">
                </div>
            </div>

            <div class="tour-request-field">
                <label class="tour-request-label" for="tourRequestDate">
                    <i class="fa-solid fa-calendar-days"></i>
                    <span>Preferred Date</span>
                </label>
                <input id="tourRequestDate" type="date" class="tour-request-control">
            </div>

            <div class="tour-request-field">
                <label class="tour-request-label">
                    <i class="fa-solid fa-tags"></i>
                    <span>Interests</span>
                </label>
                <div class="tour-request-chip-wrap" id="tourRequestChips">
                    <button type="button" class="tour-request-chip" data-interest-chip="Food">Food</button>
                    <button type="button" class="tour-request-chip" data-interest-chip="Beach">Beach</button>
                    <button type="button" class="tour-request-chip" data-interest-chip="Trek">Trek</button>
                    <button type="button" class="tour-request-chip" id="tourRequestAddInterest">+ Add</button>
                </div>
            </div>

            <div class="tour-request-field">
                <label class="tour-request-label" for="tourRequestDescription">
                    <i class="fa-solid fa-book-open"></i>
                    <span>Description</span>
                </label>
                <textarea id="tourRequestDescription" class="tour-request-control tour-request-textarea" rows="5" placeholder="Describe your travel request..." required></textarea>
            </div>

            <div class="tour-request-actions">
                <button type="button" class="tour-request-cancel" data-close-tour-request>Cancel</button>
                <button type="submit" class="tour-request-submit">Post</button>
            </div>
        </form>
    </section>
</div>
<?php /**PATH /home/mistah-regime/tribaltours/resources/views/partials/tour-request-modal.blade.php ENDPATH**/ ?>