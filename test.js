const axios = require('axios');

axios.post('https://showusyourstalent.alwaysdata.net/api/auth/login', {
  email: 'promoteur@showusyourtalent.com',
  password: '12345678'
}, {
  withCredentials: true // important pour les cookies/session
})
.then(res => console.log(res.data))
.catch(err => console.error(err.response.data));

