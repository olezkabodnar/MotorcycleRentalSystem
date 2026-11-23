<div class="ui segment">
    <h2 class="ui header">
        <i class="calendar alternate icon"></i>
        <div class="content">
            Bookings
            <div class="sub header">All reservations, newest first.</div>
        </div>
    </h2>

    <?php if ($data['created']): ?>
        <div class="ui positive message">
            Reservation #<?= htmlspecialchars($data['created']) ?> was created.
        </div>
    <?php endif; ?>

    <a href="/MotorcycleRentalSystem/public/reservations/new" class="ui primary button">
        <i class="plus circle icon"></i> New Rental
    </a>

    <table class="ui celled table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Customer</th>
                <th>Motorcycle</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Price</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data['reservations'] as $reservation) { ?>
                <tr>
                    <td><?= htmlspecialchars($reservation->id) ?></td>
                    <td>
                        <?php if ($reservation->customerName == ''): ?>
                            <span class="ui grey text">Unknown customer</span>
                        <?php else: ?>
                            <?= htmlspecialchars($reservation->customerName) ?>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($reservation->motorcycleName . ' (' . $reservation->regNo . ')') ?></td>
                    <td><?= htmlspecialchars($reservation->startDate) ?></td>
                    <td><?= htmlspecialchars($reservation->endDate) ?></td>
                    <td>€<?= htmlspecialchars(number_format($reservation->price, 2)) ?></td>
                    <td>
                        <?php if ($reservation->isActive()): ?>
                            <div class="ui green label">Active</div>
                        <?php else: ?>
                            <div class="ui grey label">Cancelled</div>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
