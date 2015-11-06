<?php
/*
 * Example 9 - Using OAuth access token to list settlements of an account.
 */

try
{
	/*
	 * Initialize the Mollie API library with your OAuth access token.
	 */
	include "initialize_with_oauth.php";

	/*
	 * Get the all the settlements for this account.
	 */
	$settlements = $mollie->settlements->all();

	echo '<ul>';
	foreach ($settlements as $settlement)
	{
		echo '<li><b>Settlement ' . htmlspecialchars($settlement->reference) . ':</b> (' . htmlspecialchars($settlement->settledDatetime) . ')';
		echo '<table border="1"><tr><th>Month</th><th>Description</th><th>Count</th><th>Net</th><th>VAT</th><th>Gross</th></tr>';

		// Convert from stdClass to array
		$settlement_periods = json_decode(json_encode($settlement->periods), TRUE);

		foreach ($settlement_periods as $year => $months)
		{
			foreach ($months as $month => $monthly_settlement)
			{
				foreach ($monthly_settlement['revenue'] as $revenue)
				{
					echo '<tr>';
					echo '<td>' . htmlspecialchars($year . '-' . $month) . '</td>';
					echo '<td>' . htmlspecialchars($revenue['description']) . '</td>';
					echo '<td align="right">' . htmlspecialchars($revenue['count']) . ' x</td>';
					echo '<td align="right">' . htmlspecialchars($revenue['amount']['net'] ?: '-') . '</td>';
					echo '<td align="right">' . htmlspecialchars($revenue['amount']['vat'] ?: '-') . '</td>';
					echo '<td align="right">' . htmlspecialchars($revenue['amount']['gross'] ?: '-') . '</td>';
					echo '</tr>';
				}
				foreach ($monthly_settlement['costs'] as $revenue)
				{
					echo '<tr>';
					echo '<td>' . htmlspecialchars($year . '-' . $month) . '</td>';
					echo '<td>' . htmlspecialchars($revenue['description']) . '</td>';
					echo '<td align="right">' . htmlspecialchars($revenue['count']) . ' x</td>';
					echo '<td align="right">' . htmlspecialchars(-$revenue['amount']['net'] ?: '-') . '</td>';
					echo '<td align="right">' . htmlspecialchars(-$revenue['amount']['vat'] ?: '-') . '</td>';
					echo '<td align="right">' . htmlspecialchars(-$revenue['amount']['gross'] ?: '-') . '</td>';
					echo '</tr>';
				}
			}
		}

		echo '<tr><th colspan="5" align="right">TOTAL</th><th align="right">' . htmlspecialchars($settlement->amount) . '</th></tr>';

		echo '</table>';
		echo '</li>';
	}
	echo '</ul>';

	$settlement = $mollie->settlements->get("open");

	echo "Open amount: €{$settlement->amount}\n";
}
catch (Mollie_API_Exception $e)
{
	echo "API call failed: " . htmlspecialchars($e->getMessage());
}
