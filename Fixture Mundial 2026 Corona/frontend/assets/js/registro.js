document.addEventListener('DOMContentLoaded', () => {
  const form = document.querySelector('#registroForm');
  const area = document.querySelector('#area');
  const status = document.querySelector('#registroStatus');
  CoronaUi.populateAreas(area);

  form.addEventListener('submit', async (event) => {
    event.preventDefault();
    CoronaUi.clearMessage(status);

    const payload = Object.fromEntries(new FormData(form).entries());
    if (!payload.nombre_apellido || !CoronaUi.isEmail(payload.email) || !payload.area) {
      CoronaUi.setMessage(status, 'warning', 'Completa nombre, email valido y area.');
      return;
    }

    try {
      await CoronaApi.registerParticipant(payload);
      form.reset();
      CoronaUi.populateAreas(area);
      CoronaUi.setMessage(status, 'success', 'Registro guardado correctamente.');
    } catch (error) {
      CoronaUi.setMessage(status, 'danger', error.message);
    }
  });
});
