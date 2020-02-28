<link href="/supr-admin/html/css/bootstrap/bootstrap.min.css" rel="stylesheet" type="text/css">

<style>
  table {
    border-collapse: collapse;
  }
  td {
    padding: 5px;
  }
  a {
    color: blue;
  }

  .success {
    color: green;
  }

  .error {
    color: red;
  }
</style>

<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script>
    function exportXeroTemplate()
    {
      var payruns = '';
      $('input[name="payrunSelect"]:checked').each(function() {
         payruns = (payruns ? payruns + ',' : '') + $(this).data('payrunId');
      });

      if (payruns) {
          var firstChequeNumber = $('#firstChequeNumber').val();
          var country = '<?= $countryFilter ?>';
          var inputs = [
            'payruns=' + payruns,
            'country=' + country,
          ];

          if (firstChequeNumber) {
            inputs.push('firstChequeNumber=' + firstChequeNumber);
          }
          window.location.href = '/internaltools/payruns/queues/xeroTemplate?' + inputs.join('&');
      } else {
        alert('Please select a payrun');
      }

    }
</script>

<h1>Payrun Queues</h1>
<hr>

Page: <?= $page + 1 ?><br><br>

<?php if ($page): ?>
  <a href="/internaltools/payruns/queues/<?= ($page - 1) ?>?filter=<?= $filter ?>&country=<?= $countryFilter ?>">Prev</a> |
<?php endif; ?>

<a href="/internaltools/payruns/queues/<?= ($page + 1) ?>?filter=<?= $filter ?>&country=<?= $countryFilter ?>">Next</a>

<br><br>

<div>
  Country: &nbsp;
  <?= $countryFilter ? $countryLinks['all'] : 'ALL' ?>
  &nbsp; | &nbsp;
  <?= $countryFilter === 'usa' ? 'USA' : $countryLinks['usa'] ?>
  &nbsp; | &nbsp;
  <?= $countryFilter === 'canada' ? 'Canada' : $countryLinks['canada'] ?>
</div>

<br>

Filters: <a href="/internaltools/payruns/queues/<?= $page ?>">ALL</a> |
<a href="/internaltools/payruns/queues/<?= $page ?>?filter=no-invoice">No Invoices</a> |
<a href="/internaltools/payruns/queues/<?= $page ?>?filter=push-cheques">Push Cheques</a> |
<a href="/internaltools/payruns/queues/<?= $page ?>?filter=xero-invoice-incomplete">Xero Invoice Incomplete</a> |
<a href="/internaltools/payruns/queues/<?= $page ?>?filter=eft-credit-incomplete">EFT Credit Incomplete</a> |
<a href="/internaltools/payruns/queues/<?= $page ?>?filter=eft-debit-incomplete">EFT Debit Incomplete</a>

<br><br>

<div style="border: 1px solid; border-radius: 5px; padding: 5px">
      <h3>Push Cheques</h3>
      First Cheque Number: <input type="text" id="firstChequeNumber">
      <button type="button" class="btn btn-success" onclick="javascript:exportXeroTemplate()">Xero Template</button>
</div>

<br><br>

<table border="1">
<tr style="font-weight:bold;">
  <td>Select</td>
  <td>ID</td>
  <td>Run Datetime</td>
  <td>Approved Datetime</td>
  <td>Company ID</td>
  <td>Company Name</td>
  <td>Version</td>
  <td>Period Start</td>
  <td>Period End</td>
  <td>Pay Date</td>
  <td>Frequency</td>
  <td>Audit Clocks</td>
  <td>Ran</td>
  <td>Approved</td>
  <td>Offrun</td>
  <td>Invoice Created?</td>
  <td>City</td>
  <td>Net Pay</td>
  <td>Payments</td>
  <td>Push Issues Cheques</td>
  <!--<td>Paystub Delivery</td>-->
  <td>Xero Invoice</td>
  <td>EFT Credit</td>
  <td>EFT Debit</td>
</tr>
<?php foreach ($queues as $queue): ?>
<?php
  $changeUserLink = 'https://' . $queue->subdomain . '.pushoperations.com/payroll/change_user/' . $queue->username_md5;
?>
<tr valign=top>
  <td><input type="checkbox" name="payrunSelect" data-payrun-id="<?= $queue->id ?>"></td>
  <td><?= $queue->id ?></td>
  <td><?= date('Y-m-d g:iA', strtotime($queue->rundatetime)) ?></td>
  <td><?= $queue->approved ? date('Y-m-d g:iA', strtotime($queue->approvedDateTime)) : 'N/A' ?></td>
  <td><a href="<?= $changeUserLink ?>" target="_blank"><?= $queue->companyId ?></a></td>
  <td><?= $queue->company_name ?></td>
  <td><?= $queue->subdomain ?></td>
  <td><?= $queue->pay_period_startdate ?></td>
  <td><a href="<?= $changeUserLink ?>?redirect=/payroll/payrun_history_view/<?= $queue->pay_period_enddate ?>" target="_blank"><?= $queue->pay_period_enddate ?></a></td>
  <td><a href="<?= $changeUserLink ?>?redirect=/payroll/payrun/history_payments/<?= $queue->pay_period_enddate ?>" target="_blank"><?= $queue->paydate ?></a></td>
  <td><?= $queue->frequency ?></td>
  <td>
    <?php
      $redirect = urlencode('/analytics/reports/clockapprovals_vs_timesheets/' . $queue->pay_period_startdate . '/' . $queue->pay_period_enddate . '?noresync=true&queue-id=' . $queue->id);
    ?>
    <a href="<?= $changeUserLink ?>?redirect=<?= $redirect ?>" target="_blank">
      Audit
    </a>
    <br>
    <span class="<?= $queue->audit ? 'success' : 'error' ?>">
      <?= $queue->audit ? 'Yes ' : 'No' ?>
    </span>

    <span class="<?= $queue->num_discrepancies == 0 ? 'success' : 'error' ?>">
      <?= round($queue->num_discrepancies) ?>
    </span>

    <?php if ($queue->timesheet_approval): ?>
      <div><hr>TS Opened</div>
    <?php endif; ?>
  </td>
  <td class="<?= $queue->status ? 'success' : 'error' ?>"><?= $queue->status ? 'Yes' : 'No' ?></td>
  <td class="<?= $queue->approved ? 'success' : 'error' ?>"><?= $queue->approved ? 'Yes' : 'No' ?></td>
  <td class="<?= $queue->off_run ? 'success' : 'error' ?>"><?= $queue->off_run ? 'Yes' : '-' ?></td>
  <td class="<?= $queue->invoice ? 'success' : 'error' ?>"><?= $queue->invoice ? '<a href=' . $changeUserLink . '?redirect=/invoices/' . $queue->invoiceMd5 . ' target="_blank">Created</a>' : '-' ?></td>
  <td><?= $queue->city ?>, <?= $queue->province ?></td>
  <td class="<?= $queue->netpay_total == $queue->payments_total ? 'success' : 'error' ?>">
    <?= number_format($queue->netpay_total, 2) ?>
  </td>
  <td><?= number_format($queue->payments_total, 2) ?></td>
  <td class="<?= $queue->pushCheques ? 'success' : 'error' ?>"><?= $queue->pushCheques ? 'Yes' : 'No' ?></td>
  <!--<td class="<?= $queue->require_stub_delivery == 'yes' ? 'success' : 'error' ?>"><?= ucfirst($queue->require_stub_delivery) ?></td>-->
  <td>
    <?php if ($queue->xero_invoice): ?>
      <a style="color: green; cursor: pointer;" id="xero-<?= $queue->id ?>" data-status="complete" data-id="<?= $queue->id ?>" class="xero-click">Complete</a>
    <?php else: ?>
      <a style="color: red; cursor: pointer;" id="xero-<?= $queue->id ?>" data-status="incomplete" data-id="<?= $queue->id ?>" class="xero-click">Incomplete</a>
    <?php endif; ?>
  </td>
  <td>
    <?php if ($queue->eft_credit): ?>
      <a style="color: green; cursor: pointer;" id="eft-credit-<?= $queue->id ?>" data-status="complete" data-id="<?= $queue->id ?>" class="eft-credit-click">Complete</a>
    <?php else: ?>
      <a style="color: red; cursor: pointer;" id="eft-credit-<?= $queue->id ?>" data-status="incomplete" data-id="<?= $queue->id ?>" class="eft-credit-click">Incomplete</a>
    <?php endif; ?>
  </td>
  <td>
    <?php if ($queue->eft_debit): ?>
      <a style="color: green; cursor: pointer;" id="eft-debit-<?= $queue->id ?>" data-status="complete" data-id="<?= $queue->id ?>" class="eft-debit-click">Complete</a>
    <?php else: ?>
      <a style="color: red; cursor: pointer;" id="eft-debit-<?= $queue->id ?>" data-status="incomplete" data-id="<?= $queue->id ?>" class="eft-debit-click">Incomplete</a>
    <?php endif; ?>
  </td>
</tr>
<?php endforeach; ?>
</table>

<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>

<script>
  $(document).ready(function () {
    $('.xero-click').click(function () {
      toggleStatus($(this), 'xero_invoice');
    });

    $('.eft-credit-click').click(function () {
      toggleStatus($(this), 'eft_credit');
    });

    $('.eft-debit-click').click(function () {
      toggleStatus($(this), 'eft_debit');
    });
  });

  function postStatus(id, field, status) {
    $.post("/internaltools/payruns/queues/status",
    {
      "id": id,
      "field": field,
      "status": status,
    },
    function (data){
      //alert(data.status);
    }, "json");
  }

  function toggleStatus(row, field) {
    if (row.data('status') == 'incomplete') {
      var status = 'complete';
      var statusText = 'Complete';
      var color = 'green';
      var setStatus = 1;
    } else {
      var status = 'incomplete';
      var statusText = 'Incomplete';
      var color = 'red';
      var setStatus = 0;
    }

    row.css('color', color);
    row.html(statusText);
    row.data('status', status);
    var id = row.data('id');

    postStatus(id, field, setStatus);
  }
</script>
