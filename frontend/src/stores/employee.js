import { defineStore } from "pinia";

export const useEmployeeStore = defineStore("employeeStore", {
  state: () => ({
    employees: [],
    employee: null,
    errors: {},
  }),
  actions: {
    async getAllEmployees() {
      const res = await fetch("/api/employees", {
        headers: {
          Authorization: `Bearer ${localStorage.getItem("token")}`,
        },
      });
      return await res.json();
    },
    async getEmployee(id) {
      const res = await fetch(`/api/employees/${id}`, {
        headers: {
          Authorization: `Bearer ${localStorage.getItem("token")}`,
        },
      });
      return await res.json();
    },
    async createEmployee(formData) {
      const res = await fetch("/api/employees", {
        method: "POST",
        headers: {
          Authorization: `Bearer ${localStorage.getItem("token")}`,
          "Content-Type": "application/json",
        },
        body: JSON.stringify(formData),
      });
      return await res.json();
    },
    async updateEmployee(id, formData) {
      const res = await fetch(`/api/employees/${id}`, {
        method: "PUT",
        headers: {
          Authorization: `Bearer ${localStorage.getItem("token")}`,
          "Content-Type": "application/json",
        },
        body: JSON.stringify(formData),
      });
      return await res.json();
    },
    async deleteEmployee(id) {
      const res = await fetch(`/api/employees/${id}`, {
        method: "DELETE",
        headers: {
          Authorization: `Bearer ${localStorage.getItem("token")}`,
        },
      });
      return res.ok;
    },
  },
});
