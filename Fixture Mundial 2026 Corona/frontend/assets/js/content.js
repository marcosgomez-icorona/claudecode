document.addEventListener('DOMContentLoaded', async () => {
  const target = document.querySelector('[data-content-key]');
  if (!target) return;
  const key = target.dataset.contentKey;

  try {
    const content = await CoronaApi.getContent();
    target.innerHTML = content[key] || target.innerHTML;
  } catch (error) {
    target.insertAdjacentHTML('afterbegin', `<div class="alert alert-warning">Usando contenido estatico: ${error.message}</div>`);
  }
});
