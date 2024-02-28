import './styles/backoffice_styles.css';

require('./template/assets/vendors/js/vendor.bundle.base.js');
require('./template/assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js');
require('./template/assets/vendors/chart.js/Chart.min.js');
require('./template/assets/vendors/progressbar.js/progressbar.min.js');
require('./template/assets/js/off-canvas.js');
require('./template/assets/js/hoverable-collapse.js');
require('./template/assets/js/template.js');
require('./template/assets/js/settings.js');
require('./template/assets/js/todolist.js');
require('./template/assets/js/jquery.cookie.js');
require('./template/assets/js/dashboard.js');
require('datatables.net');
require('datatables.net-bs4');

$('#datatable').DataTable();