# Define questions for each category
questions = {
    "Money Avoidance": [
        "I do not deserve a lot of money when others have less than me.",
        "Rich people are greedy.",
        "People get rich by taking advantage of others.",
        "I do not deserve money.",
        "Good people should not care about money."
    ],
    "Money Worship": [
        "Things would get better if I had more money.",
        "More money will make you happier.",
        "It is hard to be poor and happy.",
        "You can never have enough money.",
        "Money is power."
    ],
    "Money Status": [
        "Most poor people do not deserve to have money.",
        "You can have love or money, but not both.",
        "I will not buy something unless it is new (e.g., car, house).",
        "Poor people are lazy.",
        "Money is what gives life meaning."
    ],
    "Money Vigilance": [
        "You should not tell others how much money you have or make.",
        "It is wrong to ask others how much money they have or make.",
        "Money should be saved not spent.",
        "It is important to save for a rainy day.",
        "People should work for their money and not be given financial handouts."
    ]
}

# Descriptions
descriptions = {
    "Money Avoidance": "Money Avoiders may also believe that they do not deserve money. They may believe that wealthy people are greedy or corrupt. They often believe that there is virtue in living with less money.",
    "Money Worship": "Money Worshipers believe that money is the key to happiness. They feel that the solution to their problems is to have more money. At the same time, they believe that one can never have enough money. They find that the pursuit of money never quite satisfies them.",
    "Money Status": "Money Status seekers tend to link their self-worth with their net worth. They may prioritize outward displays of wealth. This behavior can put them at risk of overspending.",
    "Money Vigilance": "The Money Vigilant are alert, watchful, and concerned about their financial health. Feeling that they have enough money is important to them. They believe it is important to save."
}

# Collect scores
scores = {category: 0 for category in questions}

print("Rate each statement from 1 to 5:\n1 = Strongly Disagree, 5 = Strongly Agree\n")

# Get user input
for category, qs in questions.items():
    print(f"\n{category}")
    for q in qs:
        while True:
            try:
                answer = int(input(f"{q}\nYour answer (1-5): "))
                if 1 <= answer <= 5:
                    scores[category] += answer
                    break
                else:
                    print("Please enter a number from 1 to 5.")
            except ValueError:
                print("Invalid input. Please enter a number from 1 to 5.")

# Show results
print("\n--- Your Results ---")
for cat, score in scores.items():
    print(f"{cat}: {score} points")

# Determine highest scoring category
top_category = max(scores, key=scores.get)

print(f"\n🏆 Dominant Money Belief: {top_category}")
print(descriptions[top_category])
