module.exports = {
  // Helper functions for load testing
  randomNumber: function(min, max) {
    return Math.floor(Math.random() * (max - min + 1)) + min;
  },

  randomEmail: function(context) {
    const emails = context.vars.memberEmails;
    return emails[Math.floor(Math.random() * emails.length)];
  },

  randomField: function(context) {
    const fields = context.vars.fields;
    return fields[Math.floor(Math.random() * fields.length)];
  },

  randomTimeSlot: function(context) {
    const slots = context.vars.timeSlots;
    return slots[Math.floor(Math.random() * slots.length)];
  },

  // Before each scenario
  beforeScenario: function(context, done) {
    console.log(`Starting scenario: ${context.scenario.name}`);
    done();
  },

  // After each scenario
  afterScenario: function(context, done) {
    console.log(`Completed scenario: ${context.scenario.name}`);
    done();
  }
};
