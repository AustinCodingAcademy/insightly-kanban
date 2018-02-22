<!DOCTYPE html>
<html>
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Insightly Lead Kanban</title>
  <link rel="stylesheet" href="skeleton-plus.min.css" type="text/css" />
  <style>
  * {
    line-height: 1rem;
    outline: none;
  }
  a {
    color: white;
    text-decoration: none;
  }
  button {
    margin: 5px;
  }
  .austin {
    background-color: #f8b232;
    border-color: #f8b232;
  }
  .sanantonio {
    background-color: #82ca9c;
    border-color: #82ca9c;
  }
  .dallas {
    background-color: #00b7d4;
    border-color: #00b7d4;
  }
  .houston {
    background-color: #f71f49;
    border-color: #f71f49;
  }
  .northaustin {
    background-color: #BF5700;
    border-color: #BF5700;
  }
  #spinner-container {
    display: flex;
    height: 100%;
    width: 100%;
    justify-content: center;
    align-items: center;
    position: absolute;
    -webkit-animation: spin 4s infinite linear;
    -moz-animation: spin 4s infinite linear;
    animation: spin 4s infinite linear;
  }
  .spinner {
    display: flex;
    justify-content: center;
    align-items: center;
  }
  .spinner:before {
    content: "\25CC";
    font-size: 128px;
  }
  @-moz-keyframes spin {
    from { -moz-transform: rotate(0deg); }
    to { -moz-transform: rotate(360deg); }
  }
  @-webkit-keyframes spin {
    from { -webkit-transform: rotate(0deg); }
    to { -webkit-transform: rotate(360deg); }
  }
  @keyframes spin {
    from {transform:rotate(0deg);}
    to {transform:rotate(360deg);}
  }
  .other, .callout:not(.dallas):not(.austin):not(.sanantonio):not(.houston):not(.northaustin) {
    background-color: #c1c1c1;
    border-color: #c1c1c1;
  }
  .off {
    background-color: #ffffff !important;
  }
  button:not(.off), .callout:not(.off) {
    color: white;
  }
  .callout {
    margin: 10px;
    padding: 10px;
  }
  .accordion p {
    display: flex;
    flex-wrap: wrap;
    overflow-y: auto;
  }
  li {
    overflow-y: auto;
  }
  .accordion ul li i:after {
    right: 0px;
  }
  .accordion ul li i:before {
    right: 0px;
    top: 0px;
  }
  small {
    font-size: 0.75rem;
  }
  .badge {
    margin: 5px;
    padding: 5px;
    border-radius: 10px;
  }
  a {
    color: #333;
  }
  a:hover {
    color: #000000;
  }
  .container {
    max-width: 100%;
  }
  nav {
    display: flex;
    justify-content: space-between;
    margin-bottom: 20px;
  }
  </style>
</head>
<body>
  <header style="margin: 30px">
    <h1 style="text-align: center">
      Insightly Lead Kanban
      <br />
      <br />
      <small>Updates every 10 minutes</small>
    </h1>
  </header>
  <main class="container"></main>
  <div id="spinner-container">
    <div class="spinner"></div>
  </div>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
  <script>
  const statusOrders = [[
    "New Lead",
    "Event Lead",
    "Application",
    "Phone Interview",
    "Assignment Sent",
    "Accepted. Welcome Packet Sent",
    "Registered Student",
    "Welcome Call",
    "WARM: No Action Taken",
    "WARM: Video Sent",
    "WARM: Email Sent",
    "WARM: Call Made",
    "WARM: Text Sent",
    "COLD",
    "Dallas Waiting List",
    "Houston Waiting List",
    "Q & A Session",
    "Campus Tour",
    "Interview Scheduled - No Show",
    "Interview Completed - Welcome Packet Sent"
  ], [
    "INSTRUCTOR: Early Interest",
    "INSTRUCTOR: Phone Interview",
    "INSTRUCTOR: Online Training Module 1 - 4",
    "INSTRUCTOR: In Person Onboarding",
    "INSTRUCTOR: AQR Completed",
    "INSTRUCTOR: Resigned"
  ]];
  const cities = [
    'austin',
    'northaustin',
    'sanantonio',
    'dallas',
    'houston'
  ];
  fetch(`https://campus.austincodingacademy.com/api/report/U0VMRUNUICoKRlJPTSBpbnNpZ2h0bHlfbGVhZHMKSk9JTiBpbnNpZ2h0bHlfbGVhZF9zdGF0dXNlcyBvbiBpbnNpZ2h0bHlfbGVhZHMuTEVBRF9TVEFUVVNfSUQgPSBpbnNpZ2h0bHlfbGVhZF9zdGF0dXNlcy5MRUFEX1NUQVRVU19JRApKT0lOIGluc2lnaHRseV9sZWFkc19DVVNUT01GSUVMRFMgb24gaW5zaWdodGx5X2xlYWRzLnlvc3FsX2lkID0gaW5zaWdodGx5X2xlYWRzX0NVU1RPTUZJRUxEUy5pbnNpZ2h0bHlfbGVhZHNfeW9zcWxfaWQ7?format=json&key=<?php echo getenv('REPORT_API_KEY'); ?>&refresh=true`, {
    method: 'GET'
  }).then(res => {
    return res.json().then(leads => {
      const $board = $('<section class="accordion row"></main>');
      const badges = cities.map(city => {
        return (`<span class="callout ${city} badge"><small></small></span>`);
      }).join('');
      statusOrders.forEach(statusOrder => {
        const statuses = {};
        const $list = $('<ul class="six columns-md"></ul>');
        leads.results.forEach(lead => {
          lead[lead['CUSTOM_FIELD_ID']] = lead['FIELD_VALUE'];
          const statusName = `${statusOrder.indexOf(lead.LEAD_STATUS) < 10 ? '0': ''}${statusOrder.indexOf(lead.LEAD_STATUS)}. ${lead.LEAD_STATUS}`;
          if (statusOrder.includes(lead.LEAD_STATUS)) {
            if (!statuses[statusName]) {
              statuses[statusName] = [];
              $list.append($(`
<li>
  <input type="checkbox" checked="true">
  <i></i>
  <h2>
    ${statusName}
    ${badges}
    <span class="callout other badge"><small></small></span>
  </h2>
  <p id="${lead.LEAD_STATUS_ID}"></p>
</li>
              `));
            }
            const foundLead = statuses[statusName].find(olead => { return olead.LEAD_ID == lead.LEAD_ID });
            if (foundLead) {
              Object.assign(foundLead, lead);
            } else {
              statuses[statusName].push(lead);
            }
          }
        });
        statusOrder.map(statusName => {
          return `${statusOrder.indexOf(statusName) < 10 ? '0': ''}${statusOrder.indexOf(statusName)}. ${statusName}`;
        }).filter(statusName => {
          return !statuses[statusName];
        }).forEach(statusName => {
          $list.append($(`
<li>
  <input type="checkbox" checked="true">
  <i></i>
  <h2>
    ${statusName}
    ${badges}
    <span class="callout other badge"><small></small></span>
  </h2>
  <p></p>
</li>
          `));
        });
        $list.html(
          $list.children().sort((a, b) => {
            const vA = $(a).find('h2').text();
            const vB = $(b).find('h2').text();
            return (vA < vB) ? -1 : (vA > vB) ? 1 : 0;
          })
        );
        $board.append($list);
        for (status in statuses) {
          statuses[status].sort((a, b) => {
            date = (status === '00. New Lead') ? 'DATE_CREATED_UTC' : 'DATE_UPDATED_UTC';
            return a[date] < b[date] ? 1 : -1;
          });
          statuses[status].forEach(lead => {
            let city =  lead.LEAD_FIELD_38 || lead.LEAD_FIELD_2 || lead.ADDRESS_CITY || 'other';
            if (
              (lead.LEAD_FIELD_37 && lead.LEAD_FIELD_37.includes('austin') && lead.LEAD_FIELD_37.includes('CSHARPDOTNET')) ||
              (lead.LEAD_FIELD_1 && lead.LEAD_FIELD_1.includes('North Austin'))
            ) {
              city = 'northaustin';
            }
            city = city.toLowerCase().split(' ').join('');
            const updatedDate = moment.utc(lead.DATE_UPDATED_UTC);
            const updatedDisplay = updatedDate.fromNow();
            const createdDate = moment.utc(lead.DATE_CREATED_UTC);
            const createdDisplay = createdDate.fromNow();
            let temp = '🔥🔥🔥';
            const temps = [
              [3, 'days', '🔥🔥'],
              [1, 'week', '🔥'],
              [10, 'days', '❄️'],
              [2, 'weeks', '❄️❄️'],
              [17, 'days', '❄️❄️❄️'],
              [3, 'weeks', '☃️'],
              [4, 'weeks', '☃️☃️'],
              [5, 'weeks', '☃️☃️☃️']
            ]
            temps.forEach(_temp => {
              const date = status === '00. New Lead' ? createdDate : updatedDate;
              if (date.isSameOrBefore(moment().subtract(_temp[0], _temp[1]))) {
                temp = _temp[2];
              }
            });
            $list.find(`#${lead.LEAD_STATUS_ID}`).append(`
              <a class="callout ${city}"
                 href="https://crm.na1.insightly.com/details/Leads/${lead.LEAD_ID}"
                 target="_blank"
                >
                <small>
                  <strong>${lead.FIRST_NAME} ${lead.LAST_NAME}</strong>
                  <br />
                  ${status === '00. New Lead' ? createdDisplay : updatedDisplay} ${temp}
                <small>
              </a>
            `);
          });
        }
      });
      $('#spinner-container').remove()
      $('main').append($board);

      $('.badge:not(.other)').each(function() {
        const num = $(this).parent().next().find(`.${$(this).attr('class').split(' ')[1]}`).length;
        $(this).find('small').text(num);
      });

      $('.badge.other').each(function() {
        const num = $(this).parent().next().children().filter('.callout:not(.houston):not(.austin):not(.sanantonio):not(.dallas):not(.northaustin)').length;
        $(this).find('small').text(num);
      });
      const buttons = cities.map(city => {
        return (`<button class="${city}">${city.toUpperCase()}</button>`);
      }).join('');
      $('main').prepend(`
        <nav>
          ${buttons}
          <button class="other">OTHER</button>
        </nav>
      `);
      $('button').click(function() {
        const city = $(this).attr('class').split(' ')[0]
        if (city === 'other') {
          $('.callout:not(.houston):not(.austin):not(.sanantonio):not(.dallas):not(.badge):not(.northaustin)').toggle();
        } else {
          $(`.callout.${city}:not(.badge)`).toggle();
        }
        $(this).toggleClass('off');
        $(`.badge.${city}`).toggleClass('off');
      });
    });
  });
  </script>
</body>
</html>
