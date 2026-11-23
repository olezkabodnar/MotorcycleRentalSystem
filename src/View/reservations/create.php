<?php
$old = $data['old'];
$oldExtras = [];
if (isset($old['extras']) && is_array($old['extras'])) {
    $oldExtras = $old['extras'];
}
?>
<div class="ui segment">
    <h2 class="ui header">
        <i class="calendar plus icon"></i>
        <div class="content">
            New Rental
            <div class="sub header">Choose the customer, motorcycle, dates and extras, then preview the price.</div>
        </div>
    </h2>

    <?php if ($data['error']): ?>
        <div class="ui negative message">
            <div class="header">Error</div>
            <p><?= htmlspecialchars($data['error']) ?></p>
        </div>
    <?php endif; ?>

    <form class="ui form" method="post" action="/MotorcycleRentalSystem/public/reservations">
        <div class="field">
            <label for="customer">Customer</label>
            <select id="customer" name="customer" required>
                <option value="">Select customer</option>
                <?php foreach ($data['customers'] as $customer) { ?>
                    <?php if ($customer->isActive()) { ?>
                        <option value="<?= htmlspecialchars($customer->id) ?>"
                            <?php if (isset($old['customer']) && $old['customer'] == $customer->id) echo 'selected'; ?>>
                            <?= htmlspecialchars($customer->fullName()) ?>
                        </option>
                    <?php } ?>
                <?php } ?>
            </select>
        </div>

        <div class="field">
            <label for="motorcycle">Motorcycle</label>
            <select id="motorcycle" name="motorcycle" required>
                <option value="">Select motorcycle</option>
                <?php foreach ($data['motorcycles'] as $motorcycle) { ?>
                    <?php if ($motorcycle->isActive()) { ?>
                        <option value="<?= htmlspecialchars($motorcycle->regNo) ?>"
                            <?php if (isset($old['motorcycle']) && $old['motorcycle'] == $motorcycle->regNo) echo 'selected'; ?>>
                            <?= htmlspecialchars($motorcycle->name() . ' (' . $motorcycle->regNo . ') - €' . $motorcycle->rate . '/day') ?>
                        </option>
                    <?php } ?>
                <?php } ?>
            </select>
        </div>

        <div class="two fields">
            <div class="field">
                <label for="start_date">Start date</label>
                <input type="date" id="start_date" name="start_date" required
                    value="<?= htmlspecialchars(isset($old['start_date']) ? $old['start_date'] : '') ?>">
            </div>
            <div class="field">
                <label for="end_date">End date (return day)</label>
                <input type="date" id="end_date" name="end_date" required
                    value="<?= htmlspecialchars(isset($old['end_date']) ? $old['end_date'] : '') ?>">
            </div>
        </div>

        <div class="grouped fields">
            <label>Extras</label>
            <?php foreach ($data['extras'] as $extra) { ?>
                <div class="field">
                    <div class="ui checkbox">
                        <input type="checkbox" id="extra<?= htmlspecialchars($extra->id) ?>" name="extras[]"
                            value="<?= htmlspecialchars($extra->id) ?>"
                            <?php if (in_array($extra->id, $oldExtras)) echo 'checked'; ?>>
                        <label for="extra<?= htmlspecialchars($extra->id) ?>">
                            <?= htmlspecialchars($extra->name) ?>
                            (€<?= htmlspecialchars($extra->price) ?>
                            <?= $extra->chargeType == 'per_day' ? 'per day' : 'once' ?>)
                        </label>
                    </div>
                </div>
            <?php } ?>
        </div>

        <?php if ($data['price']): ?>
            <div class="ui segment" id="price_breakdown">
                <h3 class="ui header">Price breakdown</h3>
                <div class="ui list">
                    <?php foreach ($data['price']->describe() as $line) { ?>
                        <div class="item"><?= htmlspecialchars($line) ?></div>
                    <?php } ?>
                </div>
                <div class="ui large label">
                    Total: €<?= htmlspecialchars(number_format($data['price']->total(), 2)) ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="ui divider"></div>

        <button class="ui button" type="submit" formaction="/MotorcycleRentalSystem/public/reservations/preview">
            <i class="calculator icon"></i> Preview Price
        </button>
        <button class="ui primary button" type="submit">
            <i class="save icon"></i> Create Reservation
        </button>
        <a href="/MotorcycleRentalSystem/public/reservations" class="ui button">Cancel</a>
    </form>
</div>
