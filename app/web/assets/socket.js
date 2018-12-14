document.getElementById('button').addEventListener('click', () => {
  const nickname = document.getElementById('nickname');

  if (nickname) {
    const url = '/api/v1/room/enter';
    const options = {
      method: 'POST',
      credentials: 'include',
      body: JSON.stringify({ nickname: nickname.value.trim() })
    };

    fetch(url, options)
      .then(() => {
        nickname.value = '';
      });
  }
});

let nicknamesCount = 0;

const conn = new ab.Session('ws://localhost:8080',
  () => {
    conn.subscribe('newNickname', (topic, data) => {
      const newLi = document.createElement('li');
      const list = document.getElementById('list');
      newLi.textContent = data.nickname;

      if (list) {
        list.appendChild(newLi);
      }

      const counter = document.getElementById('counter');

      if (counter) {
        counter.textContent = ++nicknamesCount;
      }
    });
  },
  () => {
    console.warn('WebSocket connection closed');
  },
  { skipSubprotocolCheck: true }
);
