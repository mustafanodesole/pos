<template>
  <q-page>
  <div class="q-pa-md">
    <q-table @row-click="handleRowClick" class="text-h4 text-capitalize " flat title="Products"
      dense :rows="filterData" :columns="columns" row-key="name" :grid="$q.screen.lt.sm" :rows-per-page-options="[10, 15 , 20 , 25, 30]">
      <template v-slot:top-right>
        <div class="flex flex-center">
          <q-input outlined dense v-model="searchProduct"  debounce="300" placeholder="Search by Name" style="background-color: #F2F5F8;">
            <template v-slot:append>
              <q-icon name="search" />
            </template>
          </q-input>
          <q-btn class="q-ml-sm" label="Export CSV"  @click="exportToCSV" color="green-10"/>
        </div>
      </template>



      <!-- Scoped slot for Action column -->
      <template v-slot:body-cell-Action="props">
        <Td>
          <div class="row items-center justify-center">
            <q-btn v-if="props.row.isSell" icon="close" flat class="" size="sm"
              @click.stop="props.row.isSell = false" />
            <q-input v-if="props.row.isSell" v-model="sellItems" @click.stop type="number" dense outlined class="col-2 "
              placeholder="No. of Items" />
            <q-btn v-if="props.row.isSell" flat icon="send_and_archive" color="black"
              @click.stop="handleSellItems(props.row)" class="col-1" />

            <q-btn v-if="!props.row.isSell" flat icon="sell" color="black" @click.stop="toggleSellInput(props.row)"
              class="col-6">
              <q-tooltip class="bg-black" anchor="top middle" self="bottom middle" :offset="[10, 10]">
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
          <div class="text-h6 text-capitalize">{{ selectedRow?.product_name || "Product Name" }}</div>
          <q-btn icon="close" v-close-popup @click="isRow = false" flat />
        </q-card-section>

        <q-card-section class="q-pt-none q-gutter-y-md">
          <q-input v-model="purchasedPrice" type="number" outlined label="Purchased Price" />
          <q-input v-model="retailPrice" type="number" outlined label="Retail Price" />
        </q-card-section>

        <q-card-actions align="right" class="text-primary">
          <q-btn outline label="Cancel" v-close-popup color="red"  />
          <q-btn label="Update" @click="handleUpdatePrice" v-close-popup color="black" />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </div>
</q-page>
</template>

<script setup>
import { useQuasar } from "quasar";
import {onMounted,  computed } from "vue";
import { ref } from "vue";
const $q = useQuasar();
const isRow = ref(false);
const searchProduct = ref("");
const sellItems = ref(null)
const rows = ref([]);
const sellingId = ref(null)
const selectedRow = ref(null);

const purchasedPrice = ref(null);
const retailPrice = ref(null);


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
    align: "right",
    sortable: true,
    sort: (a, b) => parseInt(a, 10) - parseInt(b, 10),
  },
  { name: "Action", align: "center", label: "Sell Item", field: "action" },
];


function handleRowClick(evt, row, index) {
  console.log(evt, row, index);

  isRow.value = true;
  selectedRow.value = row
}

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
    item.product_name.toLowerCase().includes(searchProduct.value.toLowerCase()) || item.retail_price.includes(searchProduct.value.toLocaleLowerCase())
  );
});
filterData;


// Toggle sell input for a specific row
function toggleSellInput(row) {
  // Toggle `isSell` for the selected row and reset others
  rows.value.forEach((r) => {
    r.isSell = r === row ? !r.isSell : false;
  });
  sellingId.value = row.sno;

}


const handleSellItems = async (row) => {
  let formData = new FormData();
  formData.append('soldItems', sellItems.value)
  formData.append("id", sellingId.value);

  await fetch("http://localhost:8000/soldItem.php", {
    method: "POST",
    body: formData
  }).then( (res) => res).then(async (result) => {
    console.log(result);

    sellItems.value = null;
    rows.value.forEach((r) => {
      r.isSell = r === row ? !r.isSell : false;
    });
    $q.notify({ color: "green", message: `Sold Successfully !!` , position : 'top'});
    // const soldItemsValue = parseInt(sellItems.value, 10);
    // if (!isNaN(soldItemsValue)) {
    //   row.remaining -= soldItemsValue; // Adjust the remaining count
    // }
  }).catch(error => {

    console.log(error)
    $q.notify({ color: "red", message: `Failed !! ` , position : 'top'});
  }).then(() => {
    setTimeout(() => {

      window.location.reload();
    } , 1000)

  });



}



const handleUpdatePrice = async () => {
  if (!selectedRow.value) return;

  const formData = new FormData();
  formData.append("id", selectedRow.value.sno); // Pass the row ID
  formData.append("purchased_price", purchasedPrice.value);
  formData.append("retail_price", retailPrice.value);

  try {
    const response = await fetch("http://localhost:8000/update.php", {
      method: "POST",
      body: formData,
    });

    if (!response.ok) {
      throw new Error("Failed to update price");
    }

    // Update the row data locally
    selectedRow.value.purchased_price = `Rs. ${purchasedPrice.value} /-`;
    selectedRow.value.retail_price = `Rs. ${retailPrice.value} /-`;

    $q.notify({ color: "green", message: "Price updated successfully!", position: "top" });
  } catch (error) {
    console.error(error);
    $q.notify({ color: "red", message: "Failed to update price!", position: "top" });
  } finally {
    isRow.value = false; // Close the modal


  }
};

onMounted(async () => {
  await getData();
});


</script>



<style scoped>
   tbody, tr , td{
    font-size: 50px;
  }
</style>
