import { reactive } from "vue";
import axios from "axios";

export const quickSelectionStore = reactive({
    isOpen: false,
    loading: false,
    product: null,

    async open(productSlug) {
        this.isOpen = true;
        this.loading = true;
        this.product = null;

        try {
            const response = await axios.get(
                route("products.quick-view", productSlug),
            );
            this.product = response.data.product;
        } catch (error) {
            console.error("Failed to fetch quick view data:", error);
            this.isOpen = false;
        } finally {
            this.loading = false;
        }
    },

    close() {
        this.isOpen = false;
        this.product = null;
    },
});
