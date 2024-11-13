import { defineStore } from "pinia";
import { useAuthStore } from "@/stores/auth";

export const useFinancialStore = defineStore("financialStore", {
  state: () => ({
    finances: [],
    finance: null,
    errors: {},
  }),
  actions: {
    /******************* Get all financial transactions *******************/
    async getAllFinances() {
      const res = await fetch("/api/financial-transactions", {
        headers: {
          Authorization: `Bearer ${localStorage.getItem("token")}`,
        },
      });
      const data = await res.json();
      this.finances = data;
      return data;
    },

    /******************* Get a single financial transaction *******************/
    async getFinance(id) {
      const res = await fetch(`/api/financial-transactions/${id}`, {
        headers: {
          Authorization: `Bearer ${localStorage.getItem("token")}`,
        },
      });
      const data = await res.json();
      this.finance = data;
      return data;
    },

    /******************* Create a financial transaction *******************/
    async createFinance(formData) {
      console.log(formData);
      const res = await fetch("/api/financial-transactions", {
        method: "POST",
        headers: {
          Authorization: `Bearer ${localStorage.getItem("token")}`,
        },
        body: JSON.stringify(formData),
      });

      const data = await res.json();
      
      if (data.errors) {
        this.errors = data.errors;
      } else {
        this.errors = {};
        this.finances.push(data); // Atualiza a lista de finanças com a nova transação
      }
    },

    /******************* Update a financial transaction *******************/
    async updateFinance(financeId, formData) {
      const res = await fetch(`/api/financial-transactions/${financeId}`, {
        method: "PUT",
        headers: {
          Authorization: `Bearer ${localStorage.getItem("token")}`,
        },
        body: JSON.stringify(formData),
      });

      const data = await res.json();
      if (data.errors) {
        this.errors = data.errors;
      } else {
        this.errors = {};
        // Atualiza o item atualizado na lista de finanças
        const index = this.finances.findIndex(f => f.id === financeId);
        if (index !== -1) {
          this.finances[index] = data;
        }
      }
    },

    /******************* Delete a financial transaction *******************/
    async deleteFinance(financeId) {
      const res = await fetch(`/api/financial-transactions/${financeId}`, {
        method: "DELETE",
        headers: {
          Authorization: `Bearer ${localStorage.getItem("token")}`,
        },
      });

      if (res.ok) {
        // Remove a transação deletada da lista de finanças
        this.finances = this.finances.filter(finance => finance.id !== financeId);
      } else {
        const data = await res.json();
        this.errors = data.errors || {};
      }
    },
  },
});
