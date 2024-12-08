
<?php
function showMaintenancePage($feature)
{
    echo "<div class='maintenance-container'>
        <h2>$feature is Currently Under Development</h2>
        <p>We're working hard to bring you this feature. Please check back later.</p>
        <a href='javascript:history.back()' class='btn btn-primary'>Go Back</a>
    </div>";
}
