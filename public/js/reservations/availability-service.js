const AvailabilityService = (() => {
  const ENDPOINT = '/availability/slots';

  async function fetchSlots(businessProfileId, date) {
    const params = new URLSearchParams({
      business_profile_id: String(businessProfileId),
      date: date,
    });
    const url = ENDPOINT + '?' + params.toString();

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
