const AvailabilityService = (() => {
  async function fetchSlots(userId, date) {
    const url = '/available-slots/' + encodeURIComponent(userId) + '/' + encodeURIComponent(date);

    const response = await fetch(url, {
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
      },
    });

    if (!response.ok) {
      throw new Error('Error al consultar disponibilidad (' + response.status + ')');
    }

    const data = await response.json();
    return data;
  }

  return { fetchSlots };
})();
