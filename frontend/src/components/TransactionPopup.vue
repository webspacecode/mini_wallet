<template>
  <div v-if="show" class="popup" :class="type">
    {{ message }}
    <button class="close" @click="close">&times;</button>
  </div>
</template>

<script>
export default {
  props: {
    message: String,
    show: Boolean,
    type: { type: String, default: 'success' } // 'success' | 'error'
  },
  emits: ['update:show'],
  methods: {
    close() {
      this.$emit('update:show', false);
    }
  }
};
</script>

<style scoped>
.popup {
  position: fixed;
  top: 20px;
  right: 20px;
  padding: 12px 20px;
  border-radius: 8px;
  color: white;
  box-shadow: 0 4px 6px rgba(0,0,0,0.2);
  z-index: 9999;
  display: flex;
  align-items: center;
  justify-content: space-between;
  min-width: 200px;
}
.popup.success { background-color: #4caf50; }
.popup.error { background-color: #f44336; }
.close {
  background: none;
  border: none;
  color: white;
  font-size: 16px;
  cursor: pointer;
}
</style>