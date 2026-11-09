

<div class="ui segment">
    <h2 class="ui header">
        <i class="users icon"></i>
        <div class="content">
            View All Customers
            <div class="sub header">Manage customer information from this page.</div>
        </div>
    </h2>

    <!-- Add new customer button -->
    <div class="ui padded basic segment">
        <a href="#" class="ui primary button">
            <i class="user plus icon"></i> Add New Customer
        </a>
    </div>

    <!-- Display customers in a table -->
    <table class="ui celled table">
        <thead>
            <tr>
                <th>Customer ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>License No</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data['customers'] as $customer) { ?>
                <tr>
                    <td><?= htmlspecialchars($customer->id) ?></td>
                    <td><?= htmlspecialchars($customer->fullName()) ?></td>
                    <td><?= htmlspecialchars($customer->email) ?></td>
                    <td><?= htmlspecialchars($customer->licenceNo) ?></td>
                    <td>
                        <?php if ($customer->isActive()): ?>
                            <div class="ui green label">Active</div>
                        <?php else: ?>
                            <div class="ui grey label">Inactive</div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="#" class="ui small blue button">
                            <i class="edit icon"></i> Edit
                        </a>
                        <a href="#" class="ui small red button">
                            <i class="trash icon"></i> Delete
                        </a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
