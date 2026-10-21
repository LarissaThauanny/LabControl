document.addEventListener('submit', event => {
  const form=event.target;
  if (form instanceof HTMLFormElement && form.dataset.confirm && !confirm(form.dataset.confirm)) event.preventDefault();
});
