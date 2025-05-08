# Define questions for each category
questions = {
    "Money Resentment": [
        "If I have more money than most people in society, then, I do not deserve it.",
        "All rich people achieve wealth through greed and exploitation.",
        "If I have more money than most people in society, then, I do not deserve it.",
        "If I have more money than most people in society, then, I do not deserve it.",
        "If I have more money than most people in society, then, I do not deserve it."
    ],
    "Money Fantasists": [
        "All problems can be solved by spending money.",
        "You cannot be happy unless you are rich.",
        "People who are happy must be unhappy.",
        "You should always seek to have more money.",
        "Money is a way of gaining power and influence."
    ],
    "Money Prestige": [
        "I am what I own.",
        "I need to keep up with the Joneses.",
        "When I have nice things, I have more self-esteem.",
        "People with more money are more important tha people with less money.",
        "Money makes your life more appealing."
    ],
    "Money Anxiety": [
        "Never pay for something you can do yourself.",
        "Money should be saved at all costs.",
        "I do not spend moeny when I don't have to pay.",
        "Paying for luxury is a waste.",
        "You should always keep as much money as possible for an emergency."
    ]
}

# Descriptions
descriptions = {
    "Money Resentment": "Money Avoiders may also believe that they do not deserve money. They may believe that wealthy people are greedy or corrupt. They often believe that there is virtue in living with less money.",
    "Financial Fantasists": "Money Worshipers believe that money is the key to happiness. They feel that the solution to their problems is to have more money. At the same time, they believe that one can never have enough money. They find that the pursuit of money never quite satisfies them.",
    "Money Prestige": "Money Status seekers tend to link their self-worth with their net worth. They may prioritize outward displays of wealth. This behavior can put them at risk of overspending.",
    "Money Anxiety": "The Money Vigilant are alert, watchful, and concerned about their financial health. Feeling that they have enough money is important to them. They believe it is important to save."
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
