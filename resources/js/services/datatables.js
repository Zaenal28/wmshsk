import DataTable from 'datatables.net-vue3';
import DataTablesLib from 'datatables.net-dt';

DataTable.use(DataTablesLib);

export const defaultDtOptions = {
  responsive: false,
  autoWidth: false,
  pageLength: 10,
  lengthMenu: [5, 10, 25, 50, 100],
  order: [],
  language: {
    search: "Cari:",
    searchPlaceholder: "Ketik untuk mencari...",
    lengthMenu: "Tampilkan _MENU_ data",
    info: "Menampilkan _START_ s/d _END_ dari _TOTAL_ data",
    infoEmpty: "Menampilkan 0 data",
    infoFiltered: "(disaring dari _MAX_ total data)",
    zeroRecords: "Tidak ada data yang sesuai",
    emptyTable: "Tidak ada data yang tersedia",
    paginate: {
      first: "«",
      last: "»",
      next: "›",
      previous: "‹"
    }
  }
};

export { DataTable };
