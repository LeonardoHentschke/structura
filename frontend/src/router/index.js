import { createRouter, createWebHistory } from "vue-router";

import HomeView from "@/views/HomeView.vue";
import RegisterView from "@/views/Auth/RegisterView.vue";
import LoginView from "@/views/Auth/LoginView.vue";

import DashboardView from "@/views/DashboardView.vue";
import AboutView from "@/views/AboutView.vue";

import ClientsListView from "@/views/Clients/ClientsListView.vue";
import ClientView from "@/views/Clients/ClientView.vue";

import ProjectListView from "@/views/Project/ProjectListView.vue";
import ProjectView from "@/views/Project/ProjectView.vue";

import ProjectTypeView from "@/views/ProjectType/ProjectTypeView.vue"
import ProjectSituationView from "@/views/ProjectSituation/ProjectSituationView.vue";

import FinancialListView from "@/views/Financial/FinancialListView.vue";
import FinancialView from "@/views/Financial/FinancialView.vue";

import { useAuthStore } from "@/stores/auth";

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: "/",
      name: "home",
      component: HomeView,
    },
    {
      path: "/register",
      name: "register",
      component: RegisterView,
      meta: { guest: true },
    },
    {
      path: "/login",
      name: "login",
      component: LoginView,
      meta: { guest: true },
    },
    {
      path: "/dashboard",
      name: "dashboard",
      component: DashboardView,
      meta: { auth: true },
    },
    {
      path: '/about',
      name: 'about',
      component: AboutView,
    },
    {
      path: "/clients",
      name: "clientList",
      component: ClientsListView,
      meta: { auth: true },
    },
    {
      path: "/client/:id?",
      name: "client",
      component: ClientView,
      meta: { auth: true },
    },
    {
      path: "/projects",
      name: "projectList",
      component: ProjectListView,
      meta: { auth: true },
    },
    {
      path: "/project/:id?",
      name: "project",
      component: ProjectView,
      meta: { auth: true },
    },
    {
      path: "/project-type",
      name: "projectType",
      component: ProjectTypeView,
      meta: { auth: true },
    },
    {
      path: "/project-situation",
      name: "projectSituation",
      component: ProjectSituationView,
      meta: { auth: true },
    },
    {
      path: "/financials",
      name: "financialList",
      component: FinancialListView,
    },
    {
      path: "/financial/:id?",
      name: "financial",
      component: FinancialView,
    },
  ],
});

router.beforeEach(async (to, from) => {
  const authStore = useAuthStore();
  await authStore.getUser();

  if (authStore.user && to.meta.guest) {
    return { name: "dashboard" };
  }

  if (!authStore.user && to.meta.auth) {
    return { name: "login" };
  }
});

export default router;
