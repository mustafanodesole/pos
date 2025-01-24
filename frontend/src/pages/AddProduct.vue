<template>
    <q-page>
        <h4 class="q-ma-md">Add Stock</h4>
        <div class="row">
            <div class="col-10">
                <div class="row">
                    <div class="col-lg-4 col-md-6 col-sm-8 col-12 q-mx-md">
                        <q-form method="POST" @submit="handleSubmit" class="q-gutter-y-md">
                            <q-input v-model="form.product_name" label="Product Name" type="text" name="product_name"
                                outlined />
                            <q-input v-model="form.purchased_price" label="Purchased Price" type="number"
                                name="purchased_price" outlined />
                            <q-input v-model="form.retail_price" label="Retail Price" type="number" name="retail_price"
                                outlined />

                            <q-input v-model="form.total_items" label="No. of Items Purchased" type="number"
                                name="total_items" outlined />

                            <div class="q-gutter-x-md">
                                <q-btn type="submit" label="Submit" name="save" color="blue" />
                                <q-btn label="Cancel" color="red" to="/" />
                            </div>
                        </q-form>
                    </div>
                </div>
            </div>
        </div>
    </q-page>
</template>

<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
const router = useRouter();
// Define the reactive form object
const form = ref({
    product_name: "",
    purchased_price: null,
    retail_price: null,
    total_items: null,
});

// Handle the form submission
async function handleSubmit() {
    const formData = new FormData();

    formData.append("product_name", form.value.product_name);
    formData.append("purchased_price", form.value.purchased_price);
    formData.append("retail_price", form.value.retail_price);
    formData.append("total_items", form.value.total_items);

    try {
        let response = await fetch("http://localhost:8000/add.php", {
            method: "POST",
            body: formData,
        }).then(() => router.push("/"));

        console.log("respone :: ", response);
        form.value.product_name = "";
        form.value.purchased_price = "";
        form.value.total_items = "";
        form.value.retail_price = "";
    } catch (error) {
        console.log(error);
    }
}
</script>
