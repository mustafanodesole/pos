<template>
  <div class="q-pa-md">
    <q-table
      @row-click="handleRowClick()"
      class="text-h5 text-capitalize"
      bordered
      title="Products"
      dense
      :rows="filterData"
      :columns="columns"
      row-key="name"
      :grid="$q.screen.lt.sm"
    >
      <template v-slot:top-right>
        <div class="flex flex-center">
          <q-input
            outlined
            dense
            v-model="searchProduct"
            debounce="300"
            placeholder="Search"
          >
            <template v-slot:append>
              <q-icon name="search" />
            </template>
          </q-input>
          <q-btn
            class="q-ml-sm"
            label="Export CSV"
            color="green-8"
            @click="exportToCSV"
          />
        </div>
      </template>

      <!-- Scoped slot for S.No column -->
      <template v-slot:body-cell-sno="props">
        <Td>
          <div class="row items-center justify-center q-mb-md">
            <div class="col-9 text-body1">
              {{ props.pageIndex + 1 }}
              <!-- {{ props.row.id }} -->
            </div>
          </div>
        </Td>
      </template>

      <!-- Scoped slot for Action column -->
      <template v-slot:body-cell-Action="props">
        <Td>
          <div class="flex flex-center">
            <q-btn
              icon="sell"
              flat
              class="q-mt-xs"
              color="green-10"
              @click.stop="sellItem(props.row)"
            >
              <q-tooltip
                class="bg-indigo"
                anchor="top middle"
                self="bottom middle"
                :offset="[10, 10]"
              >
                <strong class="text-capitalize">{{ props.row.product_name }}</strong>
              </q-tooltip>
            </q-btn>
          </div>
        </Td>
      </template>
    </q-table>

    <q-dialog v-model="isRow" persistent>
      <q-card style="min-width: 350px">
        <q-card-section class="flex justify-between">
          <div class="text-h6">Panadol</div>
          <q-btn icon="close" v-close-popup @click="isRow = false" flat />
        </q-card-section>

        <q-card-section class="q-pt-none q-gutter-y-md">
          <q-input
            v-model="purchasedPrice"
            type="number"
            outlined
            label="Purchased Price"
          />
          <q-input v-model="retailPrice" type="number" outlined label="Retail Price" />
        </q-card-section>

        <q-card-actions align="right" class="text-primary">
          <q-btn flat label="Cancel" v-close-popup color="red" />
          <q-btn label="Update" v-close-popup color="primary" />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </div>
</template>

<script setup>
import { useQuasar } from "quasar";
import { onMounted, computed } from "vue";
import { ref } from "vue";
const $q = useQuasar();
const isRow = ref(false);
const searchProduct = ref("");
function handleRowClick(evt, row, index) {
  console.log(evt, row, index);

  isRow.value = true;
}
const columns = [
  {
    name: "sno",
    label: "S.No",
    align: "left",
    field: (row) => row.sno,
    sortable: false, // Serial numbers are static
  },
  {
    name: "product_name",
    required: true,
    label: "Product Name",
    align: "left",
    field: (row) => row.product_name,
    format: (val) => `${val}`,
    sortable: true,
  },

  {
    name: "purchased_price",
    label: "Purchased Price",
    field: "purchased_price",
    align: "center",
    sortable: true,
    sort: (a, b) => parseInt(a, 10) - parseInt(b, 10),
  },
  {
    name: "retail_price",
    align: "center",
    label: "Retail Price",
    field: "retail_price",
    sortable: true,
  },
  {
    name: "remaining",
    label: "Remaining",
    field: "remaining",
    align: "center",
    sortable: true,
    sort: (a, b) => parseInt(a, 10) - parseInt(b, 10),
  },
  { name: "Action", align: "center", label: "Sell Item", field: "action" },
];

const rows = ref([]);

// Export data to CSV
const exportToCSV = () => {
  const csvContent = [
    [
      "S.No",
      ...columns.map((col) => (col.label == "S.No" ? (col.label = " ") : col.label)),
    ].join(","), // Adding the S.No to the header row
    ...rows.value.map((row, index) =>
      [
        index + 1,
        ...columns.map((col) =>
          col.field instanceof Function ? col.field(row) : row[col.field]
        ),
      ].join(",")
    ),
  ].join("\n");

  const blob = new Blob([csvContent], { type: "text/csv;charset=utf-8;" });
  const url = URL.createObjectURL(blob);

  const link = document.createElement("a");
  link.setAttribute("href", url);
  link.setAttribute("download", "table-data.csv");
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
};

async function getData() {
  await fetch("http://localhost:8000/select.php")
    .then((res) => res.json())
    .then((result) => {
      console.log(result.data);
      result.data.forEach((item) => {
        rows.value.push({
          sno: item.id,
          product_name: item.product_name,
          retail_price: "Rs. " + item.retail_price + " /-",
          purchased_price: "Rs. " + item.purchased_price + " /-",
          remaining: item.total_items,
        });
      });
    });
}

let filterData = computed(() => {
  if (!searchProduct.value.trim()) {
    return rows.value;
  }
  return rows.value.filter((item) =>
    item.product_name.toLowerCase().includes(searchProduct.value.toLowerCase())
  );
});
filterData;
function sellItem(row) {
  console.log("sell item", row);
}

onMounted(async () => {
  await getData();
});
</script>
