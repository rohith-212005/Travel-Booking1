// Flight Data API Service
class FlightDataService {
    constructor() {
        this.airlines = [
            'Air India', 'IndiGo', 'SpiceJet', 'Vistara', 'AirAsia India',
            'GoAir', 'Air India Express', 'Alliance Air', 'TruJet', 'Star Air'
        ];

        this.cities = {
            'Delhi': 'DEL',
            'Mumbai': 'BOM',
            'Bangalore': 'BLR',
            'Chennai': 'MAA',
            'Kolkata': 'CCU',
            'Hyderabad': 'HYD',
            'Pune': 'PNQ',
            'Ahmedabad': 'AMD',
            'Jaipur': 'JAI',
            'Lucknow': 'LKO',
            'Patna': 'PAT',
            'Guwahati': 'GAU',
            'Kochi': 'COK',
            'Thiruvananthapuram': 'TRV',
            'Goa': 'GOI',
            'Srinagar': 'SXR',
            'Chandigarh': 'IXC',
            'Amritsar': 'ATQ',
            'Varanasi': 'VNS',
            'Nagpur': 'NAG'
        };
    }

    // Generate random flight data based on from/to locations
    generateFlightData(from, to, count = 10) {
        const flights = [];
        const fromCode = this.cities[from] || 'XXX';
        const toCode = this.cities[to] || 'YYY';

        for (let i = 0; i < count; i++) {
            const airline = this.airlines[Math.floor(Math.random() * this.airlines.length)];
            const departureHour = Math.floor(Math.random() * 24);
            const departureMinute = Math.floor(Math.random() * 4) * 15; // 0, 15, 30, 45
            const duration = Math.floor(Math.random() * 3) + 1; // 1-3 hours
            const arrivalHour = (departureHour + duration) % 24;
            const arrivalMinute = Math.floor(Math.random() * 4) * 15;

            const departureTime = `${departureHour.toString().padStart(2, '0')}:${departureMinute.toString().padStart(2, '0')}`;
            const arrivalTime = `${arrivalHour.toString().padStart(2, '0')}:${arrivalMinute.toString().padStart(2, '0')}`;

            const basePrice = Math.floor(Math.random() * 5000) + 2000; // ₹2000-7000
            const price = Math.floor(basePrice / 100) * 100; // Round to nearest 100

            const flightClass = Math.random() > 0.7 ? 'Business' : 'Economy';
            const stops = Math.random() > 0.6 ? '1-Stop' : 'NON-STOP';

            flights.push({
                id: `FL${Math.floor(Math.random() * 10000)}`,
                airline: airline,
                flightNumber: `${airline.substring(0, 2).toUpperCase()}${Math.floor(Math.random() * 1000)}`,
                from: from,
                to: to,
                fromCode: fromCode,
                toCode: toCode,
                departureTime: departureTime,
                arrivalTime: arrivalTime,
                duration: `${duration}h ${Math.floor(Math.random() * 60)}m`,
                price: price,
                class: flightClass,
                stops: stops,
                availableSeats: Math.floor(Math.random() * 50) + 10
            });
        }

        // Sort by departure time
        return flights.sort((a, b) => a.departureTime.localeCompare(b.departureTime));
    }

    // Simulate API call with delay
    async fetchFlights(from, to) {
        return new Promise((resolve) => {
            setTimeout(() => {
                const flights = this.generateFlightData(from, to);
                resolve({
                    success: true,
                    data: flights,
                    timestamp: new Date().toISOString()
                });
            }, Math.random() * 1000 + 500); // 500-1500ms delay
        });
    }

    // Filter flights by time range
    filterByTimeRange(flights, timeRange) {
        const timeRanges = {
            '12am - 6am': (time) => {
                const hour = parseInt(time.split(':')[0]);
                return hour >= 0 && hour < 6;
            },
            '6am - 12pm': (time) => {
                const hour = parseInt(time.split(':')[0]);
                return hour >= 6 && hour < 12;
            },
            '12pm - 6pm': (time) => {
                const hour = parseInt(time.split(':')[0]);
                return hour >= 12 && hour < 18;
            },
            '6pm - 12am': (time) => {
                const hour = parseInt(time.split(':')[0]);
                return hour >= 18 || hour < 0;
            }
        };

        if (timeRanges[timeRange]) {
            return flights.filter(flight => timeRanges[timeRange](flight.departureTime));
        }
        return flights;
    }

    // Filter flights by class
    filterByClass(flights, flightClass) {
        return flights.filter(flight => flight.class.toLowerCase() === flightClass.toLowerCase());
    }
}

// Export for use in other files
window.FlightDataService = FlightDataService; 