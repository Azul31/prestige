 const monthYear = document.getElementById("monthYear");
  const calendarDays = document.getElementById("calendarDays");
  const prevMonthBtn = document.getElementById("prevMonth");
  const nextMonthBtn = document.getElementById("nextMonth");

  let currentDate = new Date();

  function renderCalendar(date) {
    const year = date.getFullYear();
    const month = date.getMonth();

    const firstDay = new Date(year, month, 1).getDay();
    const lastDate = new Date(year, month + 1, 0).getDate();

    const monthNames = [
      "January","February","March","April","May","June",
      "July","August","September","October","November","December"
    ];

    monthYear.textContent = `${monthNames[month]} ${year}`;
    calendarDays.innerHTML = "";

    // Day names
    const dayNames = ["Sun","Mon","Tue","Wed","Thu","Fri","Sat"];
    dayNames.forEach(d => {
      const div = document.createElement("div");
      div.className = "day-name";
      div.textContent = d;
      calendarDays.appendChild(div);
    });

    // Empty slots before first day
    for (let i = 0; i < firstDay; i++) {
      const emptyDiv = document.createElement("div");
      calendarDays.appendChild(emptyDiv);
    }

    // Days of the month
    for (let day = 1; day <= lastDate; day++) {
      const dayDiv = document.createElement("div");
      dayDiv.className = "calendar-day";
      dayDiv.textContent = day;

      const today = new Date();
      if (
        day === today.getDate() &&
        month === today.getMonth() &&
        year === today.getFullYear()
      ) {
        dayDiv.classList.add("today");
      }

      calendarDays.appendChild(dayDiv);
    }
  }

  prevMonthBtn.addEventListener("click", () => {
    currentDate.setMonth(currentDate.getMonth() - 1);
    renderCalendar(currentDate);
  });

  nextMonthBtn.addEventListener("click", () => {
    currentDate.setMonth(currentDate.getMonth() + 1);
    renderCalendar(currentDate);
  });

  renderCalendar(currentDate);
        // New user management
        function approveUser(button) {
            const userItem = button.closest('.user-item');
            userItem.style.backgroundColor = '#e8f5e8';
            userItem.style.transform = 'scale(0.95)';
            
            setTimeout(() => {
                userItem.style.opacity = '0';
                setTimeout(() => {
                    userItem.remove();
                }, 300);
            }, 500);
        }

        function rejectUser(button) {
            const userItem = button.closest('.user-item');
            userItem.style.backgroundColor = '#ffebee';
            userItem.style.transform = 'scale(0.95)';
            
            setTimeout(() => {
                userItem.style.opacity = '0';
                setTimeout(() => {
                    userItem.remove();
                }, 300);
            }, 500);
        }

        // Navigation functionality
        document.querySelectorAll('.nav-item a').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                document.querySelectorAll('.nav-item a').forEach(l => l.classList.remove('active'));
                this.classList.add('active');
                
                const page = this.textContent.trim();
                if (page !== 'Dashboard') {
                    alert(`${page} functionality would be implemented here.`);
                }
            });
        });

        // Add some dynamic behavior to cards
        document.querySelectorAll('.card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-8px) scale(1.02)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });

        // Simulate real-time updates
        function updatePatientStats() {
            const newPatients = Math.floor(Math.random() * 10) + 1;
            const oldPatients = Math.floor(Math.random() * 25) + 15;
            
            document.querySelector('.stat-card:nth-child(2) .stat-number').textContent = newPatients;
            document.querySelector('.stat-card:nth-child(3) .stat-number').textContent = oldPatients;
        }

        setInterval(updatePatientStats, 30000);

        // Add parking notification system
// Add parking notification system (improved)
function addParkingNotification(name) {
  const parkingList = document.querySelector('.parking-list');
  if (!parkingList) return;

  const initials = name.split(' ').map(n => n[0]).join('').toUpperCase();

  // create new item
  const newItem = document.createElement('div');
  newItem.className = 'parking-item';
  // start hidden for entrance animation
  newItem.style.opacity = '0';
  newItem.style.transform = 'translateY(-12px)';

  newItem.innerHTML = `
    <div class="parking-avatar">${initials}</div>
    <div class="parking-info">
      <div class="parking-name">${name} has arrived at the building</div>
    </div>
  `;

  // insert at top
  parkingList.insertBefore(newItem, parkingList.firstChild);

  // force reflow then animate to visible
  requestAnimationFrame(() => {
    newItem.style.opacity = '1';
    newItem.style.transform = 'translateY(0)';
  });

  // If list is longer than limit, remove oldest with fade
  const items = parkingList.querySelectorAll('.parking-item');
  const maxItems = 3;
  if (items.length > maxItems) {
    const oldest = items[items.length - 1];
    // add fading class to trigger CSS fade
    oldest.classList.add('fading-out');

    // remove after animation ends (match CSS transition duration)
    setTimeout(() => {
      if (oldest && oldest.parentNode) oldest.parentNode.removeChild(oldest);
    }, 350); // slightly longer than CSS transition
  }

  // keep the scroll at top so latest item is visible (optional)
  parkingList.scrollTop = 0;
}

// sample names and interval (slower so you can watch it)
const sampleNames = ['Dr. Kim Lee', 'Sarah Johnson', 'Mike Chen', 'Lisa Wang', 'David Park'];
setInterval(() => {
  if (Math.random() > 0.6) {
    const randomName = sampleNames[Math.floor(Math.random() * sampleNames.length)];
    addParkingNotification(randomName);
  }
}, 2500);
